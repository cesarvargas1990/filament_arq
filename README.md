🛒 Sistema de Ventas con Laravel, Filament y PDF

Este es un sistema de gestión de ventas construido con Laravel 11, Filament 3 y DomPDF. Permite gestionar productos, clientes y ventas, con la opción de generar facturas en PDF.

🚀 Requisitos

Antes de instalar el proyecto, asegúrate de tener:

PHP 8.1+

Composer 2.x

Node.js (Opcional, si usas frontend adicional)

Base de datos SQLite / MySQL / PostgreSQL (configurable en .env)

⚙️ Instalación

1️⃣ Clonar el Repositorio

git clone https://github.com/usuario/proyecto.git
cd proyecto

2️⃣ Instalar Dependencias

composer install

3️⃣ Configurar el Archivo .env

Crea el archivo .env basado en .env.example:

cp .env.example .env

Luego, **edita el archivo **`` y configura la conexión a la base de datos.

Ejemplo para SQLite:

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

Si usas MySQL, edita:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_bd
DB_USERNAME=usuario
DB_PASSWORD=contraseña

4️⃣ Generar la Clave de la Aplicación

php artisan key:generate

5️⃣ Migrar la Base de Datos y Poblarla con Datos Iniciales

php artisan migrate --seed

🖥️ Ejecutar el Proyecto

Inicia el servidor de desarrollo de Laravel:

php artisan serve

Luego, accede a:

🔗 http://localhost:8000/admin

Usuario de prueba (Administrador):

Email: admin@example.com

Contraseña: password

📜 Funcionalidades

✔️ Gestión de Productos (CRUD con Filament)✔️ Gestión de Clientes (Creación rápida desde ventas)✔️ Gestión de Ventas (Relación con clientes y productos)✔️ Restricción de Accesos (Roles: Administrador y Vendedor)✔️ Filtración de Productos (Vendedores solo ven productos de sus categorías)✔️ Generación de Facturas en PDF✔️ Filament Admin Panel integrado

📄 Generación de Facturas en PDF

Para generar un PDF de una venta, sigue estos pasos:

1️⃣ Ve a Filament Admin → Ventas2️⃣ En la tabla, haz clic en Descargar PDF3️⃣ Se generará un archivo PDF con los detalles de la venta

🛠️ Comandos Útiles

Actualizar dependencias

composer update

Reiniciar la base de datos

php artisan migrate:fresh --seed

Depurar y limpiar caché

php artisan optimize:clear

Ver las rutas del proyecto

php artisan route:list

🎯 Créditos

Desarrollado por [Tu Nombre]📧 Contacto: tuemail@example.com

🚀 ¡Listo! Ya puedes gestionar ventas y generar facturas en PDF con Laravel y Filament!