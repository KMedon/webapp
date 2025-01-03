<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita35350afe8e639891e2ea5c4e5f946b5
{
    public static $prefixLengthsPsr4 = array (
        'b' => 
        array (
            'backend\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'backend\\' => 
        array (
            0 => __DIR__ . '/../..' . '/backend',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita35350afe8e639891e2ea5c4e5f946b5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita35350afe8e639891e2ea5c4e5f946b5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita35350afe8e639891e2ea5c4e5f946b5::$classMap;

        }, null, ClassLoader::class);
    }
}
