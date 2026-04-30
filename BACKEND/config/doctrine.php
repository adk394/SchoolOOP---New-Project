<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/../vendor/autoload.php';

$isDevMode = true;
$paths = [
    __DIR__ . '/../src/Domain/Entity',
    __DIR__ . '/../src/Auth/Domain',
];

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

$connectionParams = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../var/school.sqlite',
];

$connection = DriverManager::getConnection($connectionParams, $config);
$connection->executeStatement(
    'CREATE TABLE IF NOT EXISTS users (id TEXT PRIMARY KEY, google_id TEXT UNIQUE, email TEXT UNIQUE, name TEXT NOT NULL)'
);

return new EntityManager($connection, $config);
