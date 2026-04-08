# School Management (Simple DDD + Doctrine)

Proyecto sencillo de gestion escolar en PHP, con enfoque DDD basico.

## Casos de uso implementados

1. CreateStudent
2. CreateCourse
3. CreateSubject
4. CreateTeacher
5. EnrollStudent
6. AssignTeacherToSubject

Extras opcionales:

- DeleteStudent
- DeleteCourse
- DeleteSubject
- DeleteTeacher

## Requisitos

- PHP 8.2+
- Composer

## Instalacion

```bash
composer install
```

## Crear base de datos SQLite con Doctrine

```bash
mkdir -p var
php bin/setup-db.php
```

## Ejecutar tests PHPUnit

```bash
./vendor/bin/phpunit
```

Los tests esenciales estan en `tests/Domain` y `tests/Application`.
Los tests opcionales extra estan en `tests/Extra`.

## Ejecutar app web

```bash
php -S localhost:8000 -t public
```

Abrir en navegador: http://localhost:8000

En la pagina principal tienes los formularios para los 6 casos de uso.
