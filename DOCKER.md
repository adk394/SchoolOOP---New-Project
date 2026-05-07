# Docker Setup - School OOP Project (SQLite)

Este proyecto está configurado para ejecutarse con Docker usando **2 contenedores** y **SQLite** como base de datos:
- **Frontend**: PHP 7.4 + Apache (puerto 8080)
- **Backend**: PHP 8.2 + Apache (puerto 8000)  
- **Base de datos**: SQLite (archivo persistente en volumen Docker)

## Ventajas de usar SQLite

- **Solo 2 contenedores** (más simple y rápido)
- **Sin configuración de BD** (no hay usuarios, passwords ni conexiones que configurar)
- **Base de datos en archivo** (fácil de respaldar o versionar)
- **Perfecto para desarrollo** y proyectos de práctica

## Requisitos

- Docker Desktop instalado
- Docker Compose (viene con Docker Desktop)

## Iniciar el proyecto

1. **Posicionarse en el directorio del proyecto**:
   ```bash
   cd SchoolOOP---New-Project
   ```

2. **Copiar el archivo de variables de entorno**:
   ```bash
   cp .env.example .env
   ```

3. **Iniciar los contenedores**:
   ```bash
   docker-compose up -d
   ```

4. **Crear las tablas de la base de datos** (solo la primera vez):
   ```bash
   docker exec -it school_backend php bin/setup-db.php
   ```

5. **Acceder a la aplicación**:
   - Frontend: http://localhost:8080
   - Backend API: http://localhost:8000

## Comandos útiles

```bash
# Ver logs de todos los contenedores
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f backend
docker-compose logs -f frontend

# Detener los contenedores
docker-compose down

# Detener y eliminar volúmenes (borra la base de datos SQLite)
docker-compose down -v

# Reconstruir las imágenes
docker-compose up -d --build

# Entrar al contenedor del backend
docker exec -it school_backend bash

# Ver la base de datos SQLite
docker exec -it school_backend sqlite3 /var/www/html/var/school.sqlite
```

## Estructura

```
.
├── docker-compose.yml      # Configuración de los 2 servicios
├── .env.example            # Variables de entorno de ejemplo
├── DOCKER.md               # Este archivo
├── FRONTEND/
│   ├── Dockerfile          # Imagen del frontend
│   └── ...
└── BACKEND/
    ├── Dockerfile          # Imagen del backend
    ├── config/doctrine.php # Configuración de SQLite
    └── ...
```

## Persistencia de datos

El archivo SQLite (`school.sqlite`) se guarda en un volumen Docker llamado `db_data`, por lo que:
- ✅ Los datos **se mantienen** al reiniciar los contenedores
- ✅ Los datos **se pierden** si ejecutas `docker-compose down -v`
- ✅ Puedes copiar el archivo SQLite para respaldarlo

## Notas

- La primera vez debes ejecutar `docker exec -it school_backend php bin/setup-db.php` para crear las tablas
- Los cambios en el código se reflejan automáticamente (volumen montado)
- Si quieres empezar desde cero: `docker-compose down -v` y vuelve a iniciar
