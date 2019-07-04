<?php

/*
 * Plugin Name: Всплывающие окна <a href="https://github.com/fancyapps/fancybox/">fancybox 3</a>
 * Plugin URI: https://github.com/nikolays93/fancybox-modals/releases
 * Description: Модальные (всплывающие) окна для создания галерей при помощи самой популярной библиотеки, всплывающих форм, блоков, галерей и сообщений.
 * Version: 1.3
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fb-modal
 * Domain Path: /languages/
 */

namespace NikolayS93\FBModals;

use NikolayS93\WPAdminPage as Admin;

if( !defined( 'ABSPATH' ) ) exit('You shall not pass');

if( !defined(__NAMESPACE__ . '\PLUGIN_DIR') ) define(__NAMESPACE__ . '\PLUGIN_DIR', __DIR__);
if( !defined(__NAMESPACE__ . '\PLUGIN_FILE') ) define(__NAMESPACE__ . '\PLUGIN_FILE', __FILE__);

if( !defined('FB3M_MODAL_COUNT_META') ) define('FB3M_MODAL_COUNT_META', '_count');

require_once ABSPATH . "wp-admin/includes/plugin.php";
require_once PLUGIN_DIR . '/vendor/autoload.php';

/**
 * Uniq prefix
 */
if(!defined(__NAMESPACE__ . '\DOMAIN')) define(__NAMESPACE__ . '\DOMAIN', Plugin::get_plugin_data('TextDomain'));
if(!defined(__NAMESPACE__ . '\SECURITY')) define(__NAMESPACE__ . '\SECURITY', DOMAIN);

add_action( 'plugins_loaded', __NAMESPACE__ . '\register_plugin_page', 10 );
function register_plugin_page() {

    /** @var Admin\Page */
    $Page = new Admin\Page( Plugin::get_option_name(), __('Modals', DOMAIN), array(
        'parent'      => '', // woocommerce
        'icon_url'    => 'dashicons-external',
        'menu' => __('Modals', DOMAIN),
        // 'validate'    => array($this, 'validate_options'),
        'permissions' => 'manage_options',
        'columns'     => 2,
    ) );

    // $Page->set_assets( function() {} );

    $Page->set_content( function() {
        Plugin::get_admin_template('menu-page', false, $inc = true);
    } );

    // $Page->add_section( new Admin\Section(
    //     'Section',
    //     __('Section'),
    //     function() {
    //         Plugin::get_admin_template('section', false, $inc = true);
    //     }
    // ) );

    $Page->add_metabox( new Admin\Metabox(
        'lib_settings',
        __('Lib settings', DOMAIN),
        function() {
            Plugin::get_admin_template('metabox1', false, $inc = true);
        },
        $position = 'side',
        $priority = 'high'
    ) );
}

register_activation_hook   ( PLUGIN_FILE, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
register_deactivation_hook ( PLUGIN_FILE, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );
register_uninstall_hook    ( PLUGIN_FILE, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );

require PLUGIN_DIR . '/include/ajax.php';
require PLUGIN_DIR . '/include/post-type.php';
require PLUGIN_DIR . '/include/metaboxes.php';
require PLUGIN_DIR . '/include/shortcode.php';