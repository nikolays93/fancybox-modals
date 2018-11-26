<?php

namespace NikolayS93\FBModals;

/**
 * Register Post type
 */
add_action('init', __NAMESPACE__ . '\register_modal_types');
function register_modal_types() {
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

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\register_modal_admin_script');
function register_modal_admin_script() {
    $screen = get_current_screen();

    /**
     * @todo add filter or action (for localize script?)
     */
    if( !empty($screen->post_type) && Utils::get_post_type_name() === $screen->post_type ) {
        wp_enqueue_script( 'admin_modal_post_type', Utils::get_plugin_url('/admin/assets/edit-post.js'), array('jquery') );
    }
}

/**
 * Add metabox
 */
$mb = new WP_Post_Boxes( array( Utils::get_post_type_name() ) );
$mb->add_fields( array('_trigger_type', '_disable_ontime') );
$mb->add_box( __( 'Event', DOMAIN ), function() {
    include PLUGIN_DIR . '/admin/template/post-metabox.php';
}, $side = true );

$mb = new WP_Post_Boxes( array( Utils::get_post_type_name() ) );
$mb->add_fields( array('_modal_type') );
$mb->add_box( __( 'Type', DOMAIN ), function() {
    include PLUGIN_DIR . '/admin/template/post-metabox-type.php';
}, $side = true );

add_action('edit_form_after_title', __NAMESPACE__ . '\render_second_title');
function render_second_title() {
    global $post;

    if( Utils::get_post_type_name() != get_post_type( $post ) ) return;

    $hide = get_post_meta($post->ID, '_hide_title', true);
    ?>
    <style>
        #hide-title__wrap {
            position: absolute;
            right: 8px;
            top: 10px;
        }
        #hide-title input {
            margin-left: 5px;
        }
    </style>

    <div id="hide-title__wrap">
        <label id="hide-title">
            <?php echo __('Do not show title', DOMAIN); ?>
            <input
                type="checkbox"
                name="hide_title"
                value="1"
                class="align-right"
                <?php checked($hide, '1');?> />
        </label>
    </div>
    <?php
}

/**
 * Save metabox fields
 */
add_action( 'save_post', __NAMESPACE__ . '\save_trigger_field' );
function save_trigger_field( $post_id ) {
    if ( ! isset( $_POST['_wp_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_metabox_nonce'], WP_Post_Boxes::SECURITY ) )
        return $post_id;

    $trigger = '';
    if( !empty( $_POST['_shortcode'] ) ) $trigger = $_POST['_shortcode'];
    if( !empty( $_POST['_onclick'] ) )   $trigger = $_POST['_onclick'];
    if( !empty( $_POST['_onload'] ) )    $trigger = $_POST['_onload'];
    if( !empty( $_POST['_onclose'] ) )   $trigger = $_POST['_onclose'];

    if( $trigger ) {
        update_post_meta( $post_id, '_trigger', sanitize_text_field($trigger) );
    }
    else {
        delete_post_meta( $post_id, '_trigger' );
    }

    if( !empty( $_POST['hide_title'] ) ) {
        update_post_meta( $post_id, '_hide_title', 1 );
    }
    else {
        delete_post_meta( $post_id, '_hide_title' );
    }
}
