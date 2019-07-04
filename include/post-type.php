<?php

namespace NikolayS93\FBModals;

/**
 * Register Post type
 */
add_action('init', __NAMESPACE__ . '\modal_register_type');
function modal_register_type() {
    $post_type_labels = array(
        'name'               => _x( 'Modal windows', DOMAIN, 'post_type' ),
        'singular_name'      => _x( 'Modal window', DOMAIN, 'post_type' ),
        'add_new'            => _x( 'Добавить всплывающее окно', DOMAIN, 'post_type' ),
        'add_new_item'       => _x( 'Добавление всплывающего окна', DOMAIN, 'post_type' ),
        'edit_item'          => _x( 'Редактирование всплывающего окна', DOMAIN, 'post_type' ),
        'new_item'           => _x( 'Новое всплывающее окно', DOMAIN, 'post_type' ),
        'view_item'          => _x( 'Смотреть всплывающее окно', DOMAIN, 'post_type' ),
        'search_items'       => _x( 'Искать всплывающее окно', DOMAIN, 'post_type' ),
        'not_found'          => _x( 'Не найдено', DOMAIN, 'post_type' ),
        'not_found_in_trash' => _x( 'Не найдено в корзине', DOMAIN, 'post_type' ),
    );

    $post_type = array(
        'label'               => null,
        'labels'              => apply_filters('FBModal_post_type_labels', $post_type_labels),
        'description'         => '',
        'public'              => false,
        'publicly_queryable'  => null,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => false,
        'show_in_rest'        => null,
        'rest_base'           => null,
        'menu_position'       => null,
        'menu_icon'           => null,
        'hierarchical'        => false,
        'supports'            => array('title', 'editor'),
        'taxonomies'          => array(),
        'has_archive'         => false,
        'rewrite'             => true,
        'query_var'           => true,
    );

    register_post_type( Utils::get_post_type_name(), $post_type );
}


/**
 * Add custom admin script for post edit page
 */
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\modal_register_admin_script');
function modal_register_admin_script() {
    $screen = get_current_screen();

    /**
     * @todo add filter or action (for localize script?)
     */
    if( !empty($screen->post_type) && Utils::get_post_type_name() === $screen->post_type ) {
        wp_enqueue_script( 'admin_modal_post_type', Utils::get_plugin_url('/admin/assets/edit-post.js'), array('jquery') );
    }
}
