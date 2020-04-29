<?php

/* @var \Psr\Container\ContainerInterface $container */
use MY\Base\App;

if (!function_exists('_h')) {
    function _h(...$args)
    {
        return App::H(...$args);
    }
}
if (!function_exists('url')) {
    function url(...$args)
    {
        return App::URL(...$args);
    }
}
