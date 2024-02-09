<?php
declare(strict_types=1);

use Pest\Plugin;
use Pest\Sharding\TestFinder\PestTestFinder;
use Pest\Sharding\TestFinder\TestFinder;
use Pest\Support\Container;

Plugin::$callables[] = function () {
    $container = Container::getInstance();

    $container->add(TestFinder::class, new PestTestFinder());
};
