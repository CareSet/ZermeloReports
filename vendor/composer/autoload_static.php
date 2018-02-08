<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbc0382a95d6e40bcb783964adc026483
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'CareSet\\CareSetReportEngine\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'CareSet\\CareSetReportEngine\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbc0382a95d6e40bcb783964adc026483::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbc0382a95d6e40bcb783964adc026483::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
