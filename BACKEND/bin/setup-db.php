<?php

//usar para arrancar la bd con el school.sqlite, luego se puede eliminar este archivo!!!

declare(strict_types=1);

use Doctrine\ORM\Tools\SchemaTool;

require_once __DIR__ . '/../vendor/autoload.php';

$entityManager = require __DIR__ . '/../config/doctrine.php';
$metadata = $entityManager->getMetadataFactory()->getAllMetadata();

if ($metadata === []) {
    echo "No hay metadatos de entidades.\n";
    exit(1);
}

$schemaTool = new SchemaTool($entityManager);
$schemaTool->dropSchema($metadata);
$schemaTool->createSchema($metadata);

echo "Base de datos creada en var/school.sqlite\n";
