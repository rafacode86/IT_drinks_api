# IT Drinks API

API RESTful desarrollada en **Laravel 12** para la gestión de **cócteles e ingredientes**, con autenticación mediante **Laravel Passport (OAuth2)**.  
Permite registrar usuarios, iniciar sesión y gestionar cócteles según el rol del usuario.

---

## Características principales

- Autenticación con **Laravel Passport**
- Roles: `admin` y `user`
- CRUD de **Ingredientes** y **Cócteles**
- Relación *many-to-many* entre cócteles e ingredientes
- Cálculo automático del contenido alcohólico (% ABV) de cada cóctel
- Endpoint de búsqueda de cócteles por ingrediente
- Documentación interactiva con **Swagger UI**

---

## Tecnologías

- PHP 8.2+
- Laravel 12
- Laravel Passport 12
- MySQL
- Swagger (l5-swagger)
- PHPUnit (tests de endpoints y roles)

---

## Instalación

### Clonar el repositorio

-bash
git clone https://github.com/tuusuario/it_drinks_api.git
cd it_drinks_api

-Instalar dependencias:
 bash
composer install

Configurar variables de entorno
Copia el archivo .env.example y crea uno nuevo:

bash
cp .env .env
Edita las variables según tu configuración local (MySQL, app URL, etc.):

env
APP_NAME=IT_Drinks_API
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_drinks_api
DB_USERNAME=root
DB_PASSWORD=

Generar la clave de aplicación:
bash
php artisan key:generate

Migrar la base de datos:
bash
php artisan migrate

Instalar Passport:
bash
php artisan passport:install
Guarda los valores de los clients que genera (personal access y password grant).

Autenticación
El sistema usa Bearer Tokens generados con Passport.

Registro:
POST /api/register

json
{
  "name": "Rafa",
  "email": "rafa@example.com",
  "password": "12345678",
  "password_confirmation": "12345678"
}
Respuesta (201)

json
{
  "message": "Usuario registrado correctamente",
  "user": { "id": 1, "name": "Rafa", "role": "user" },
  "token": "eyJ0eXAiOiJKV1QiLCJh..."
}

Login:
POST /api/login

json
{
  "email": "rafa@example.com",
  "password": "12345678"
}
Respuesta (200)

json
{
  "message": "Inicio de sesión correcto",
  "user": { "id": 1, "name": "Rafa", "role": "user" },
  "token": "eyJ0eXAiOiJKV1QiLCJh..."
}

Usa el token en cada petición protegida:
makefile

Authorization: Bearer <token>
Endpoints principales
Método	Ruta	Descripción	Rol
POST	/api/register	Registrar usuario	Público
POST	/api/login	Iniciar sesión	Público
GET	/api/ingredients	Listar ingredientes	user, admin
POST	/api/ingredients	Crear ingrediente	admin
PUT	/api/ingredients/{id}	Editar ingrediente	admin
DELETE	/api/ingredients/{id}	Eliminar ingrediente	admin
GET	/api/cocktails	Listar cócteles	user, admin
POST	/api/cocktails	Crear cóctel con ingredientes	admin
GET	/api/cocktails/{id}	Ver detalles de un cóctel	user, admin
GET	/api/v1/search/{ingredient_id}	Buscar cócteles por ingrediente	user, admin
GET	/api/v1/alcohol/{cocktail_id}	Calcular contenido alcohólico	user, admin

--Documentación Swagger
Generar la documentación:

bash
php artisan l5-swagger:generate
Abrir en el navegador:
http://127.0.0.1:8000/api/documentation

Tests:
Ejecutar todos los tests:

bash
php artisan test
Ejecutar tests de una clase específica:

bash
php artisan test --filter=IngredientTest

Estructura general:
app/
 ├── Http/
 │   ├── Controllers/
 │   │   ├── Api/
 │   │   │   ├── AuthController.php
 │   │   │   ├── CocktailController.php
 │   │   │   └── IngredientController.php
 │   │   └── Controller.php
 │   └── Middleware/CheckRole.php
 ├── Models/
 │   ├── Cocktail.php
 │   ├── Ingredient.php
 │   └── User.php
database/
 ├── migrations/
 ├── seeders/
tests/
 └── Feature/
     ├── AuthAndRoleTest.php
     ├── IngredientTest.php
     └── CocktailTest.php
     
Roles y permisos:
Admin → CRUD completo de ingredientes y cócteles.

User → Solo puede listar y ver detalles.

Invitado → No tiene acceso.

Cálculo del contenido alcohólico:
Cada cóctel tiene varios ingredientes con su volumen (measure_ml) y contenido de alcohol (alcohol_content).
​
Ejemplo:
50 ml vodka (40%) + 100 ml zumo (0%) → ABV = 13.33%
