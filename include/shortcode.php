<?php

namespace NikolayS93\FBModals;

if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

class Shortcode
{
    private static $bootstraps = array();
    private function __construct(){}

    private static function push_active_bootstraps()
    {
        self::$bootstraps += get_posts( array(
            'post_status' => 'publish',
            'post_type'  => Utils::get_post_type_name(),
            'meta_query' => array(
                array(
                    'key'     => '_trigger_type',
                    'value'   => array('onclick', 'onload', 'onclose'),
                    'compare' => 'IN',
                )
            )
        ) );
    }

    static function shortcode( $atts = array(), $content = '' )
    {
        $atts = shortcode_atts( array(
            'id'      => 0,
            'href'    => '',
            'class'   => '',
            'attr_id' => '',
            'title'   => '',
        ), $atts, Utils::get_shortcode_name() );

        if( ! $content || 0 >= $modal_id = absint($atts['id']) ) {
            return false;
        }

        self::$bootstraps[] = get_post( $modal_id );

        // sanitize attributes
        $attributes = array_map('esc_attr', apply_filters( 'FBModals_sc_attrs', array(
            'href'  => $atts['href'],
            'id'    => $atts['attr_id'],
            'class' => $atts['class'],
            'title' => $atts['title'],
        ) ) );

        $strAttributes = '';
        foreach ($attributes as $attr_key => $attr_value) {
            $strAttributes .= " $attr_key=$attr_value";
        }

        $html = sprintf('<a data-fancybox data-modal-id="%1$d" data-src="#modal_%1$d" href="%2$s"%3$s>%4$s</a>',
            $modal_id,
            $atts['href'] ? esc_url( $atts['href'] ) : '#',
            $strAttributes,
            $content
        );

        return apply_filters( 'FBModals_sc_html', $html );
    }

    /********************** After collect all bootstraps **********************/
    private static function render_modal_bootstraps()
    {
        foreach (self::$bootstraps as $modal) {
            if( empty($modal->ID) ) continue;

            $type = get_post_meta( $modal->ID, '_modal_type', true );
            if( 'inline' != $type ) continue;

            echo "<div id='modal_{$modal->ID}' style='display: none;'>";

            do_action( 'FBModal_head', $modal, $type );

            do_action( 'FBModal_body', $modal, $type );

            do_action( 'FBModal_foot', $modal, $type );

            echo "</div>";
        }
    }

    private static function enqueue_modal_scripts()
    {
        if( !is_array(self::$bootstraps) || !sizeof(self::$bootstraps) ) return false;

        $assets = Utils::get_plugin_url('/assets');
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

        $gSettings = wp_parse_args(Utils::get(), array(
            'selector' => '',
            'lib_args' => array(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce( 'Secret' ),
            'cookie'   => 'fb3m_disabled',
            'expires'  => apply_filters( 'disable_coockie_expires', 24 * 7 ), // one week
            'buttons'  => array(
                "zoom",
                //"share",
                "slideShow",
                "fullScreen",
                //"download",
                // "thumbs",
                "close"
            ),
            'lang'     => 'ru',
            'i18n'     => array(
                'CLOSE'       => "Закрыть",
                'NEXT'        => "Следующий",
                'PREV'        => "Предыдущий",
                'ERROR'       => "Контент по запросу не найден. <br/> Пожалуйста, попробуйте позже.",
                'PLAY_START'  => "Запустить слайдшоу",
                'PLAY_STOP'   => "Остановить слайдшоу",
                'FULL_SCREEN' => "На весь экран",
                'THUMBS'      => "Эскизы",
                'DOWNLOAD'    => "Скачать",
                'SHARE'       => "Поделиться",
                'ZOOM'        => "Приблизить"
            ),
        ));

        $script = $assets . "/fancybox3/jquery.fancybox{$affix}.js";
        $style = $assets . "/fancybox3/jquery.fancybox{$affix}.css";

        if( apply_filters( 'use_cdn_for_fancybox_enqueue', false ) ) {
            $script = "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.2/jquery.fancybox{$affix}.js";
            $style = "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.2/jquery.fancybox{$affix}.css";
        }

        /**
         * @todo  Use deregister?
         */
        // if( !apply_filters('disable_fancybox_enqueue', false) ) {
            wp_enqueue_script(
                'fancybox3',
                apply_filters( 'fancybox_enqueue_script', $script ),
                array('jquery'),
                '3.0',
                true
            );
            wp_enqueue_style(
                'fancybox3',
                apply_filters( 'fancybox_enqueue_style', $style ),
                null,
                '3.0'
            );
        // }

        $modals = array();
        foreach (self::$bootstraps as $modal) {
            $type = get_post_meta( $modal->ID, '_modal_type', true );

            $modals[ $modal->ID ] = array(
                'trigger_type'   => get_post_meta( $modal->ID, '_trigger_type', true ),
                'trigger'        => get_post_meta( $modal->ID, '_trigger', true ),
                'disable_ontime' => get_post_meta( $modal->ID, '_disable_ontime', true ),
                'modal_type'     => $type,
            );

            if( 'script' == $type ) {
                ob_start();
                do_action( 'FBModal_head', $modal, $type );
                do_action( 'FBModal_body', $modal, $type );
                do_action( 'FBModal_foot', $modal, $type );
                $modals[ $modal->ID ]['src'] = ob_get_clean();
            }
        }

        wp_enqueue_script( 'FB3Modals_public', $assets . '/public.js', array('jquery'), Plugin::$data['Version'], true );

        wp_localize_script( 'FB3Modals_public', 'FBModals', $modals );
        wp_localize_script( 'FB3Modals_public', 'FBM_Settings', $gSettings );
    }

    /**
     * @hook wp_footer
     */
    static function setup_footer()
    {
        self::push_active_bootstraps();
        self::render_modal_bootstraps();
        self::enqueue_modal_scripts();
        // Так не пойдет потому что wp_enqueue_scripts уже выполнен
        // add_action( 'wp_enqueue_scripts',
        //     array(__NAMESPACE__ . '\\' . __CLASS__, '_enqueue_modal_scripts') );
    }

    static function modal_window_body( $modal, $type )
    {
        switch ( $type ) {
            case 'ajax':
            echo '<div style="min-width: 400px;" id="ajax_data_'.$modal->ID.'"> '. __( 'Loading..' ) .' </div>';
            break;

            case 'inline':
            default:
            echo apply_filters( 'the_content', $modal->post_content );
            break;
        }
    }

    static function modal_window_head( $modal, $type )
    {
        if( $modal && !$hide_title = get_post_meta( $modal->ID, '_hide_title', true ) ) {
            echo "<h2>{$modal->post_title}</h2>";
        }
    }
}
