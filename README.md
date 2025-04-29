# OCM API

A RESTful API built with Laravel for managing posts data.

## Features

-   Fetch and store posts from external API
-   Paginated post listings
-   Search functionality
-   MySQL database integration
-   Dockerized development environment

## Setup Instructions

```bash
git clone https://github.com/ste7/ocm-api.git
cd ocm-api
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```
