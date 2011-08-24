<?php

require_once __DIR__.'/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'BadaBoom\Tests'                => __DIR__.'/tests/BadaBoom',
    'BadaBoom'                      => __DIR__.'/lib',
    'Symfony'        => __DIR__.'/vendor',
    
));
$loader->registerPrefixes(array(
    'UniversalErrorCatcher_' => __DIR__.'/vendor/FormaPro/UniversalErrorCatcher/src'
));
$loader->register();