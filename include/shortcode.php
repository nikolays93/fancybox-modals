<?php

namespace NikolayS93\FBModals;

if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

class Shortcode
{
    private static $instance = null;
    private $bootstraps = array();

    private function __construct() {}
    private function __clone() {}

    public static function __instance()
    {
        if( !self::$instance ) self::$instance = new self();

        return self::$instance;
    }

    function register()
    {
        add_shortcode( Utils::get_shortcode_name(), array($this, 'callback') );
    }

    function setup_footer()
    {
        $this->push_active_bootstraps();
        $this->render_modal_bootstraps();
        $this->enqueue_modal_scripts();
    }

    function callback( $atts = array(), $content = '' )
    {
        /**
         * Sanitize shortcode atts
         */
        $atts = shortcode_atts( array(
            'id'      => 0,
            'href'    => '',
            'class'   => '',
            'attr_id' => '',
            'title'   => '',
        ), $atts, Utils::get_shortcode_name() );

        /** @var int $post->ID */
        $modal_id = absint($atts['id']);

        /** We need clickable content and valid modal id for bootstrap */
        if( !$content || 0 >= $modal_id || isset($this->bootstraps[ $modal_id ]) ) return false;

        /** Insert new post by id */
        if( $post = get_post( $modal_id ) ) {
            $this->bootstraps[ $modal_id ] = $post;
        }

        $attributes = array_filter( array(
            'href'  => $atts['href'],
            'id'    => $atts['attr_id'],
            'class' => $atts['class'],
            'title' => $atts['title'],
        ) );

        $strAttributes = '';
        foreach ($attributes as $k => $v) {
            $strAttributes .= sprintf(' %s="%s"', $k, esc_attr($v));
        }

        $html = sprintf('<a data-modal-id="%1$d" href="%2$s"%3$s>%4$s</a>',
            $modal_id,
            $atts['href'] ? esc_url( $atts['href'] ) : '#',
            $strAttributes,
            $content
        );

        return $html;
    }

    private function push_active_bootstraps()
    {
        $this->bootstraps += get_posts( array(
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

    function render_modal_bootstrap($bs, $type = 'inline')
    {
        ?>
        <div id="modal-<?= $bs->ID ?>" style='display: none;'>
        <?php

            if( !$second_title = get_post_meta( $bs->ID, '_second_title', true ) ) {
                printf('<%1$s>%2$s</%1$s>', 'h4', $second_title);
            }

            switch ( $type ) {
                case 'ajax':
                echo '<div style="min-width: 400px;" id="ajax_data_'.$bs->ID.'"> '. __( 'Loading..' ) .' </div>';
                break;

                case 'inline':
                default:
                /**
                 * the content filter may be not worked on elementor
                 */
                echo apply_filters( 'the_content', $bs->post_content );
                break;
            }

        ?>
        </div><!-- #modal-<?= $bs->ID ?> -->
        <?php
    }

    private function render_modal_bootstraps()
    {
        /** @var WP_Post $bs */
        foreach ($this->bootstraps as $bs)
        {
            $type = get_post_meta( $bs->ID, '_modal_type', true );
            if( 'script' == $type || 'ajax' == $type ) continue;

            $this->render_modal_bootstrap($bs, $type);
        }
    }

    private function enqueue_modal_scripts()
    {
        $assets = Utils::get_plugin_url('/assets');
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

        $gSettings = wp_parse_args(Utils::get(), array(
            'selector' => '',
            'lib_args' => array(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce( 'Secret' ),
            'cookie'   => 'fb3m_disabled',
            'expires'  => apply_filters( 'disable_cookie_expires', 24 * 7 ), // one week
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

        $isEnqueue = false;

        if( 1 <= sizeof($this->bootstraps) ) {
            $isEnqueue = true;
        }

        elseif( !empty($gSettings['selector']) || !empty($gSettings['force']) || !empty($gSettings['gallery']) ) {
            $isEnqueue = true;
        }

        if( apply_filters( 'fancybox_assets_enqueue', $isEnqueue ) ) {

            if( apply_filters( 'fancybox_enqueue', true ) ) {
                $script = $assets . "/fancybox/jquery.fancybox{$affix}.js";
                $style = $assets . "/fancybox/jquery.fancybox{$affix}.css";

                if( apply_filters( 'use_cdn_for_fancybox_enqueue', false ) ) {
                    $script = "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.2/jquery.fancybox{$affix}.js";
                    $style = "https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.2/jquery.fancybox{$affix}.css";
                }

                wp_enqueue_script('fancybox', apply_filters('fancybox_script', $script), array('jquery'), '3.0', true);
                wp_enqueue_style('fancybox', apply_filters('fancybox_style', $style), null, '3.0');
            }

            wp_enqueue_script( 'FB3Modals_public', $assets . '/public.js', array('jquery'), Plugin::$data['Version'], true );
            wp_localize_script('FB3Modals_public', 'FBM_Settings', $gSettings);
        }

        $modals = array();
        foreach ($this->bootstraps as $bs)
        {
            $type = get_post_meta( $bs->ID, '_modal_type', true );

            $modals[ $bs->ID ] = array(
                'trigger_type'   => get_post_meta( $bs->ID, '_trigger_type', true ),
                'trigger'        => get_post_meta( $bs->ID, '_trigger', true ),
                'disable_ontime' => get_post_meta( $bs->ID, '_disable_ontime', true ),
                'modal_type'     => $type,
            );

            if( 'script' == $type ) {
                ob_start();
                $this->render_modal_bootstrap($bs, $type);
                $modals[ $bs->ID ]['src'] = ob_get_clean();
            }
        }

        wp_localize_script('FB3Modals_public', 'FBModals', $modals);
    }
}

$Shortcode = Shortcode::__instance();
$Shortcode->register();

add_action('wp_footer', array($Shortcode, 'setup_footer'));
