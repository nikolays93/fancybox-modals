<?php

namespace NikolayS93\WPAdminPage;

class Metabox
{
    private $page_instance;
    private $metabox;

    function __construct( $handle, $label, $callback, $position = 'side', $priority = 'high' ) {
        if( !is_callable( $callback ) )
            return false;

        $this->metabox = array(
            'handle'    => $handle,
            'label'     => $label,
            'callback'  => $callback,
            'position'  => $position,
            'priority'  => $priority,
        );
    }

    function init_on( $page )
    {
        if( !isset($this->metabox['handle']) ) return;

        $this->page_instance = $page;
        add_action( 'add_meta_boxes', array($this, '__box_action') );
    }

    function __box_action()
    {
        $this->metabox['screen'] = $this->page_instance->get_wp_screen_name();

        $m = $this->metabox;
        add_meta_box( $m['handle'], $m['label'], $m['callback'], $m['screen'], $m['position'], $m['priority']);
    }
}

