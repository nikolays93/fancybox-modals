<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8bb591997cd78eef0aa887d6a9ce993d
{
    public static $prefixLengthsPsr4 = array (
        'N' => 
        array (
            'NikolayS93\\WPAdminPage\\' => 23,
            'NikolayS93\\WPAdminForm\\' => 23,
            'NikolayS93\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'NikolayS93\\WPAdminPage\\' => 
        array (
            0 => __DIR__ . '/..' . '/NikolayS93/wp-admin-page/src',
        ),
        'NikolayS93\\WPAdminForm\\' => 
        array (
            0 => __DIR__ . '/..' . '/NikolayS93/wp-admin-form/src',
        ),
        'NikolayS93\\' => 
        array (
            0 => __DIR__ . '/..' . '/NikolayS93/wp-admin-table',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8bb591997cd78eef0aa887d6a9ce993d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8bb591997cd78eef0aa887d6a9ce993d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
