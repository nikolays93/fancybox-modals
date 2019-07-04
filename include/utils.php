<?php

namespace NikolayS93\FBModals;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

class Utils extends Plugin
{
    public static function get_post_type_name()
    {
        return apply_filters( "get_{DOMAIN}_post_type_name", DOMAIN );
    }

    public static function get_shortcode_name()
    {
        return apply_filters( "get_{DOMAIN}_shortcode_name", DOMAIN );
    }
}
