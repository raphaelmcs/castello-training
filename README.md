# Projeto

## Setup

cp .env.example .env

docker compose up -d

php artisan key:generate
php artisan migrate
