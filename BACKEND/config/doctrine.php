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

$isDevMode = true;
$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

// Configuración de SQLite
$varDir = __DIR__ . '/../var';
if (!is_dir($varDir)) {
    mkdir($varDir, 0777, true);
}

$dbPath = getenv('DB_PATH') ?: $varDir . '/school.sqlite';

$connectionParams = [
    'driver' => 'pdo_sqlite',
    'path' => $dbPath,
];

try {
    $connection = DriverManager::getConnection($connectionParams, $config);
    
    // Crear tabla users si no existe (Doctrine no maneja esta tabla)
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
    throw new \RuntimeException('Error de base de datos: ' . $e->getMessage());
}
