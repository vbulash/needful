<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2f4d6b129f93507baed2a94698fb64c6
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Predis\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Predis\\' => 
        array (
            0 => __DIR__ . '/..' . '/predis/predis/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2f4d6b129f93507baed2a94698fb64c6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2f4d6b129f93507baed2a94698fb64c6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2f4d6b129f93507baed2a94698fb64c6::$classMap;

        }, null, ClassLoader::class);
    }
}
