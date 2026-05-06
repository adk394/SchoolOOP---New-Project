<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . '/../vendor/autoload.php';

$paths = [
    __DIR__ . '/../src/Domain/Entity',
    __DIR__ . '/../src/Auth/Domain',
];

$config = ORMSetup::createAttributeMetadataConfiguration($paths, true);

$varDir = __DIR__ . '/../var';
if (!is_dir($varDir)) {
    mkdir($varDir, 0777, true);
}

$dbPath = $varDir . '/school.sqlite';
$connectionParams = [
    'driver' => 'pdo_sqlite',
    'path' => $dbPath,
];

try {
    $connection = DriverManager::getConnection($connectionParams, $config);
    
    $connection->executeStatement('
        CREATE TABLE IF NOT EXISTS users (
            id VARCHAR(36) PRIMARY KEY NOT NULL, 
            google_id VARCHAR(255) UNIQUE, 
            email VARCHAR(255) UNIQUE NOT NULL, 
            name VARCHAR(255) NOT NULL
        )
    ');
    
    return new EntityManager($connection, $config);
} catch (\Throwable $e) {
    throw new \RuntimeException('error base de datos: ' . $e->getMessage());
}
