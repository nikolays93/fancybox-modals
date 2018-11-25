<?php

namespace NikolayS93\FBModals;

use NikolayS93\WPAdminForm\Form as Form;

global $post;

$shortcode = sprintf('[%1$s id="%2$d"]%3$s[/%1$s]',
    Utils::get_shortcode_name(),
    $post->ID,
    __('Open', DOMAIN));

$shortcode_tpl = "<pre id='shortcode_tpl'>&lt;?php echo do_shortcode('<br><span class='no-break'>$shortcode</span><br>'); ?></pre>";

$help = __('Copy the code above to the desired page, or paste the following code into the template',
    DOMAIN) . '<br>';

$data = array(
    array(
        'id'    => '_trigger_type',
        'type'  => 'select',
        // 'label' => 'Trigger',
        'input_class' => 'widefat button',
        'options' => array(
            'shortcode' => __('On click on [shortcode]\'s content', DOMAIN),
            'onclick'   => __('On click on selector', DOMAIN),
            'onload'    => __('Page on load, after (sec)', DOMAIN),
            'onclose'   => __('When user try to close tab', DOMAIN),
        ),
    ),
    array(
        'id'    => '_shortcode',
        'type'  => 'text',
        'desc'  => $help . $shortcode_tpl,
        'input_class' => 'widefat',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
        ),
        'value' => $shortcode,
    ),
    array(
        'id'    => '_onclick',
        'type'  => 'text',
        'desc'  => __('Insert CSS/jQuery selector for "Click" event', DOMAIN),
        'input_class' => 'widefat',
        'placeholder' => '#selector',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
        ),
    ),
    array(
        'id'    => '_onload',
        'type'  => 'text',
        'desc'  => __('Insert time (seconds count), open window after page on load', DOMAIN),
        'input_class' => 'widefat',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
        ),
    ),
    array(
        'id'    => '_disable_ontime',
        'type'  => 'number',
        'label' => sprintf('<%1$s>%2$s</%1$s>',
            'strong',
            __('Disallow show window', DOMAIN)
        ),
        'desc'  => sprintf('%s. <span style="color: #f00;">%s</span>',
            __('Insert the number of hours the window will be disabled', DOMAIN),
            __('Need the cookies', DOMAIN)
        ),
        'custom_attributes' => array(
            'onclick' => 'select(this)',
            'style' => 'width: 50px; float: right;',
            'min' => 0,
        ),
        'placeholder' => '0',
    ),
);

$form = new Form( $data, $args = array(
    'is_table' => false,
    'admin_page' => false,
    'postmeta' => true,
) );

/**
 * @todo repair it!
 */
$active = $form->get( array('postmeta' => true) );
$active[ '_' . $active['_trigger_type'] ] = get_post_meta( $post->ID, '_trigger', true );
$form->set($active);

$form->display();
