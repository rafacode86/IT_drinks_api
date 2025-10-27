# IT_drinks_api que empiece el juego:

API RESTful para la gestión de cócteles e ingredientes, desarrollada con **Laravel 12** y **Laravel Passport 12** para autenticación mediante tokens.

# Tecnologías principales:
  - PHP 8.2
  - Laravel 12
  - Laravel Passport 12
  - MySQL
  - PHPUnit

# Instalación y configuración
1- Clonar repositorio:
---bash---
git clone 'https://github.com/rafacode86/IT_drinks_api.git'
cd it_drinks_api

2- Instalar dependencias:
  composer install

3- Configura la base de datos:
  DB_CONNECTION=mysql
  DB_DATABASE=it_drinks_api
  DB_USERNAME=root
  DB_PASSWORD=

4- Generar la clave de la aplicación:
  php artisan key:generate

5- Ejecutar las migraciones:
  php artisan migrate

6- Instalar Passport:
  php artisan passport:install

