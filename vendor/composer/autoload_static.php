<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit32bce4ca3709f7e108cf8a66a8b1001f
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Whoops\\' => 7,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'L' => 
        array (
            'Lion\\LionRouter\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Whoops\\' => 
        array (
            0 => __DIR__ . '/..' . '/filp/whoops/src/Whoops',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'Lion\\LionRouter\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit32bce4ca3709f7e108cf8a66a8b1001f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit32bce4ca3709f7e108cf8a66a8b1001f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit32bce4ca3709f7e108cf8a66a8b1001f::$classMap;

        }, null, ClassLoader::class);
    }
}
