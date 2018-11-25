<?php

namespace NikolayS93\FBModals;

use NikolayS93\WPListTable as Table;

$table = new Table( array(
    'singular' => 'modal',
    'plural' => 'modals',
) );

$table->set_columns( array(
    'shortcode'    => __( 'Shortcode', DOMAIN ),
    '_count'       => __( 'Show Count', DOMAIN ),
    'post_author'  => __( 'Author', DOMAIN ),
    'post_date'    => __( 'Date', DOMAIN ),
) );

// @todo repair it
// $table->set_sortable_columns();

$table->set_bulk_actions();

$posts = get_posts( array(
    'post_type' => Utils::get_post_type_name(),
) );

foreach ($posts as $post) {
    $post_meta = get_post_meta( $post->ID );

    $table->set_value( array(
        'ID'           => $post->ID,
        'title'        => esc_html( $post->post_title ),
        'shortcode'    => sprintf('[%1$s id="%2$d"]%3$s[/%1$s]', Utils::get_shortcode_name(), $post->ID, __('Open', DOMAIN)),
        '_count'       => !empty($post_meta['_count']) && is_array($post_meta['_count'])
            ? current($post_meta['_count']) : '0',
        'post_author'  => $post->post_author,
        'post_date'    => $post->post_modified,
    ) );
}

$table->prepare_items();
$table->display();

printf('
    <a href="%s" class="button button-primary" style="margin-top: 5px;">%s</a>
    <input type="hidden" name="page" value="%s" />',
    get_admin_url() . 'post-new.php?post_type=' . Utils::get_post_type_name(),
    __('Create', DOMAIN),
    $_REQUEST['page']
);

printf( '<input type="hidden" name="page" value="%s" />', $_REQUEST['page'] );