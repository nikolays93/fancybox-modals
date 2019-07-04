# wp-admin-page

```
/**
 * Class Name: WPAdminPage
 * Class URI: https://github.com/nikolays93/wp-admin-page/
 * Description: Create a new custom admin page.
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
```

### How to use: ###
```php

use NikolayS93\WPAdminPage as Admin;

function __admin_assets() {
  // WP Enqueues here..
}

$page = new Admin\Page(
    'pageSlug',
    __('New page', 'my-plugin-domain'),
    array(
        'parent'      => false,
        'icon_url'    => 'dashicons-external',
        'menu'        => __('Modals', DOMAIN),
        'permissions' => 'manage_options',
        'columns'     => 2,
    )
);

$page->set_assets( '__admin_assets' );

$page->set_content( function() {
    echo "Test content";
} );

```

### Metaboxes: ###
```php
$metabox1 = new Admin\Metabox(
    'metabox1',
    __( 'Metabox1', 'my-plugin-domain' ),
    function() {
        echo "Metabox 1 content";
    },
    $position = 'side',
    $priority = 'high'
);

$page->add_metabox( $metabox1 );
```

### @Hooks: ###
    $pageslug . _after_title (default empty hook)
    $pageslug . _before_form_inputs (default empty hook)
    $pageslug . _inside_page_content
    $pageslug . _inside_side_container
    $pageslug . _inside_advanced_container
    $pageslug . _after_form_inputs (default empty hook)
    $pageslug . _after_page_wrap (default empty hook)
