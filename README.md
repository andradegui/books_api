# books_api

## Subir servidor
- symfony serve -d
- symfony serve

## Derrubar servidor
- symfony server:stop

## Configuração docker 
- docker pull postgres
- docker run -p 5432:5432 -v C:\Users\Guilherme\Documents\estudos\dev\postgres:/var/lib/postgresql/data -e POSTGRES_PASSWORD=1234 -d postgres
- habilitar extension=pdo_pgsql no php.ini
- docker-compose up -d
- docker ps

### Parar BD 
- docker stop -t 0 *id da img*