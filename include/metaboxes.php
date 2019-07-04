<?php

namespace NikolayS93\FBModals;

add_action( 'add_meta_boxes', __NAMESPACE__ . '\modal_add_meta_boxes' );
function modal_add_meta_boxes() {
    add_meta_box('modal-meta-box-event', __( 'Event', DOMAIN ), function() {
        include Plugin::get_admin_template('post-metabox');
    }, Utils::get_post_type_name(), 'side' );

    add_meta_box('modal-meta-box-type', __( 'Type', DOMAIN ), function() {
        include Plugin::get_admin_template('post-metabox-type');
    }, Utils::get_post_type_name(), 'side' );
}

/**
 * Save metabox fields
 */
add_action( 'save_post', __NAMESPACE__ . '\modal_save_post' );
function modal_save_post( $post_id ) {
    if ( !isset( $_POST['modal_metabox_nonce'] ) || !wp_verify_nonce( $_POST['modal_metabox_nonce'], SECURITY ) ) {
        return $post_id;
    }

    $fields = array('_trigger_type', '_disable_ontime', '_modal_type', 'hide_title');

    foreach ($fields as $field)
    {
        if( !empty($_POST[$field]) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
        else {
            delete_post_meta( $post_id, $field );
        }
    }

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
}
