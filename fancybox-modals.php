<?php

/*
 * Plugin Name: FancyBox 3 Modals
 * Plugin URI: #
 * Description: Модальные (всплывающие) окна для создания галерей при помощи самой популярной библиотеки, всплывающих форм, блоков, галерей и сообщений.
 * Version: 0.1
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fancybox-modal
 * Domain Path: /languages/
 */

namespace NikolayS93\FBModals;

use NikolayS93\WPAdminPage as Admin;

if ( !defined( 'ABSPATH' ) ) exit('You shall not pass');

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (version_compare(PHP_VERSION, '5.3') < 0) {
    throw new \Exception('Plugin requires PHP 5.3 or above');
}

class Plugin
{
    public static $data;
    protected static $options;

    private function __construct() {}
    private function __clone() {}

    /**
     * Get option name for a options in the Wordpress database
     */
    public static function get_option_name()
    {
        return apply_filters("get_{DOMAIN}_option_name", DOMAIN);
    }

    /**
     * Define required plugin data
     */
    public static function define()
    {
        self::$data = get_plugin_data(__FILE__);

        if( !defined(__NAMESPACE__ . '\DOMAIN') ) define(__NAMESPACE__ . '\DOMAIN', self::$data['TextDomain']);
        if( !defined(__NAMESPACE__ . '\PLUGIN_DIR') ) define(__NAMESPACE__ . '\PLUGIN_DIR', __DIR__);
        if( !defined('FB3M_MODAL_COUNT_META') ) define('FB3M_MODAL_COUNT_META', '_count');
    }

    /**
     * include required files
     */
    public static function initialize()
    {
        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );

        require PLUGIN_DIR . '/include/utils.php';

        require PLUGIN_DIR . '/vendor/wp-post-boxes.php';
        $autoload = PLUGIN_DIR . '/vendor/autoload.php';
        if( file_exists($autoload) ) include $autoload;

        require PLUGIN_DIR . '/include/ajax.php';
        require PLUGIN_DIR . '/include/register.php';
        require PLUGIN_DIR . '/include/shortcode.php';
    }

    public static function hooks()
    {
        $class = __NAMESPACE__ . '\Shortcode';
        add_action( 'FBModal_body', array($class, 'modal_window_head'), 10, 2 );
        add_action( 'FBModal_body', array($class, 'modal_window_body'), 10, 2 );

        add_action('wp_footer', array($class, 'setup_footer'));

        add_shortcode( Utils::get_shortcode_name(), array($class, 'shortcode') );
    }

    static function activate()
    {
        add_option(
            self::get_option_name(),
            array(
                'lib_prop' => array(
                    'openCloseEffect' => 'zoom',
                    'nextPrevEffect' => 'slide',
                ),
            )
        );
    }

    static function uninstall() { delete_option( self::get_option_name() ); }

    // public static function _admin_assets()
    // {
    // }

    public static function admin_menu_page()
    {
        $page = new Admin\Page(
            Utils::get_option_name(),
            __('Modals', DOMAIN),
            array(
                'parent'      => false,
                'icon_url'    => 'dashicons-external',
                'menu'        => __('Modals', DOMAIN),
                'permissions' => 'manage_options',
                'columns'     => 2,
            )
        );

        // $page->set_assets( array(__CLASS__, '_admin_assets') );

        $page->set_content( function() {
            Utils::get_admin_template('menu-page.php', false, $inc = true);
        } );

        $metabox1 = new Admin\Metabox(
            'lib_settings',
            __( 'Lib settings', DOMAIN ),
            function() {
                Utils::get_admin_template('metabox1.php', false, $inc = true);
            },
            $position = 'side',
            $priority = 'high'
        );

        $page->add_metabox( $metabox1 );
    }
}

Plugin::define();

// register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
// register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'initialize' ), 10 );
add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'admin_menu_page' ), 10 );
add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'hooks' ), 10 );
