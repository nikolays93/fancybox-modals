<?php

namespace NikolayS93\FBModals;

use NikolayS93\WPAdminForm\Form as Form;

$data = array(
    array(
        'type'      => 'text',
        'id'        => 'selector',
        'label'     => sprintf('<hr><%2$s>%1$s</%2$s><br>',
            __('jQuery Selector', DOMAIN),
            'strong'),
        'placeholder'   => '.fancybox, .zoom',
        'custom_attributes' => array(
            'onclick' => 'if(!this.value)this.value=jQuery(this).attr(\'placeholder\');focus()',
        ),
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_args][openCloseEffect',
        'label'   => sprintf('<%2$s>%1$s</%2$s><br>', __('Show effect', DOMAIN), 'strong'),
        'options' => array(
            'false'       => __('Without effect', DOMAIN),
            'zoom'        => __('Zoom', DOMAIN),
            'fade'        => __('Fade', DOMAIN),
            'zoom-in-out' => __('Zoom in out', DOMAIN),
        ),
        'default' => 'zoom',
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_args][nextPrevEffect',
        'label'   => sprintf('<%2$s>%1$s</%2$s><br>', __('Prev/Next effect', DOMAIN), 'strong'),
        'options' => array(
            'false'       => __('Without effect', DOMAIN),
            'fade'        => __('Fade', DOMAIN),
            'slide'       => __('Slide', DOMAIN),
            'circular'    => __('Circular', DOMAIN),
            'tube'        => __('Tube', DOMAIN),
            'zoom-in-out' => __('Zoom in out', DOMAIN),
            'rotate'      => __('Rotate', DOMAIN),
        ),
        'default' => 'fade',
    ),
    array(
        'type'    => 'html',
        'id'      => 'for_group',
        'value'   => __('To group objects, use the same <em>rel</em>', DOMAIN),
    ),
);

$form = new Form( $data, array(
    'is_table' => false,
) );
$form->display();

submit_button( 'Сохранить', 'primary right', 'save_changes' );

echo '<div class="clear"></div>';