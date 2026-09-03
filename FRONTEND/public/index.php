<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require __DIR__ . '/../vendor/autoload.php';

use Jenssegers\Blade\Blade;
use Jenssegers\Blade\Container as BladeContainer;
use Illuminate\Container\Container as IlluminateContainer;

$views = __DIR__ . '/../views';
$cache = __DIR__ . '/../cache';

if (!is_dir($cache)) {
    mkdir($cache, 0777, true);
}

$container = new BladeContainer();
IlluminateContainer::setInstance($container);

$blade = new Blade($views, $cache, $container);

$apiBase = 'http://localhost:8000/api';

echo $blade->render('app', ['apiBase' => $apiBase]);
