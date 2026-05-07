# docker amb sqlite

2 contenedors: frontend (8080) y backend (8000)

## per arrancar

```bash
cp .env.example .env
docker-compose up -d
```

## urls

- frontend: http://localhost:8080
- api: http://localhost:8000

## comandes utils

```bash
# veure logs
docker-compose logs -f

# aturar
docker-compose down

# borrar bd
docker-compose down -v

# reconstruir
docker-compose up -d --build
```

## notas

- el sqlite es guarda al volumen db_data
- per crear taules primer cop: `docker exec school_backend php bin/setup-db.php`
