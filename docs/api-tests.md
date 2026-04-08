# Evidencias de tests funcionales API

Fecha de pruebas: 2026-04-08
Base URL usada: `http://127.0.0.1:8001`

## Preparacion

```bash
php bin/setup-db.php
php -S 127.0.0.1:8001 -t public
```

## Endpoints base

1. `GET /api`
- Esperado: `200`
- Obtenido: `200`
- Respuesta: `{"message":"School API running"}`

2. `GET /api/health`
- Esperado: `200`
- Obtenido: `200`
- Respuesta: `{"status":"ok"}`

## Students

1. `POST /api/students`
- Body:
```json
{
  "name": "Ana 1775663264",
  "email": "ana1775663264@example.com"
}
```
- Esperado: `201`
- Obtenido: `201`

2. `GET /api/students`
- Esperado: `200`
- Obtenido: `200`
- Respuesta de ejemplo:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Ana",
      "email": "ana@example.com",
      "course_id": null
    },
    {
      "id": 2,
      "name": "Ana 1775663264",
      "email": "ana1775663264@example.com",
      "course_id": null
    }
  ]
}
```

## Teachers

1. `POST /api/teachers`
- Body:
```json
{
  "name": "Pepe 1775663264",
  "email": "pepe1775663264@example.com"
}
```
- Esperado: `201`
- Obtenido: `201`

2. `GET /api/teachers`
- Esperado: `200`
- Obtenido: `200`

## Subjects

1. `POST /api/subjects`
- Body:
```json
{
  "name": "DWEC API",
  "course_id": 1
}
```
- Esperado: `201`
- Obtenido: `201`

2. `GET /api/subjects`
- Esperado: `200`
- Obtenido: `200`

## Casos de error

1. `POST /api/subjects` con `course_id` inexistente
- Body:
```json
{
  "name": "DWEC",
  "course_id": 999
}
```
- Esperado: `404`
- Obtenido: `404`
- Respuesta: `{"error":"Course not found"}`

2. `GET /api/unknown`
- Esperado: `404`
- Obtenido: `404`
- Respuesta: `{"error":"Endpoint not found"}`

## Comandos curl usados

```bash
curl -s http://127.0.0.1:8001/api
curl -s http://127.0.0.1:8001/api/health
curl -s -X POST http://127.0.0.1:8001/api/students -H 'Content-Type: application/json' -d '{"name":"Ana 1775663264","email":"ana1775663264@example.com"}'
curl -s http://127.0.0.1:8001/api/students
curl -s -X POST http://127.0.0.1:8001/api/teachers -H 'Content-Type: application/json' -d '{"name":"Pepe 1775663264","email":"pepe1775663264@example.com"}'
curl -s http://127.0.0.1:8001/api/teachers
curl -s -X POST http://127.0.0.1:8001/api/subjects -H 'Content-Type: application/json' -d '{"name":"DWEC API","course_id":1}'
curl -s http://127.0.0.1:8001/api/subjects
curl -s -X POST http://127.0.0.1:8001/api/subjects -H 'Content-Type: application/json' -d '{"name":"DWEC","course_id":999}'
```
