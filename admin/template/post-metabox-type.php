<?php

namespace NikolayS93\FBModals;

use NikolayS93\WPAdminForm\Form as Form;

global $post;

$data = array(
    array(
        'id'    => '_modal_type',
        'type'  => 'select',
        'label' => __('Modal type', DOMAIN),
        'input_class' => 'widefat button',
        'options' => array(
            'inline' => __('Inline', DOMAIN),
            'script' => __('Script', DOMAIN),
            // 'ajax'   => __('Ajax', DOMAIN),
        ),
    ),
);

$form = new Form( $data, $args = array(
    'is_table' => false,
    'admin_page' => false,
    'postmeta' => true,
) );

// $active = $form->get( array('postmeta' => true) );
// $active[ '_' . $active['_trigger_type'] ] = get_post_meta( $post->ID, '_trigger', true );
// $form->set($active);

$form->display();
