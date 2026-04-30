<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/../vendor/autoload.php';

$isDevMode = true;
$paths = [__DIR__ . '/../src/Domain/Entity'];

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

$connectionParams = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../var/school.sqlite',
];

$connection = DriverManager::getConnection($connectionParams, $config);

return new EntityManager($connection, $config);
