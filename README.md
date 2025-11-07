# P3 Global – Gestión de tickets

Aplicación Laravel 10 orientada a la gestión del ciclo de vida de tickets operativos. Se apoya en Livewire 3 para las interfaces reactivas y en el paquete de permisos de Spatie para controlar el acceso a vistas y acciones administrativas.

## Requisitos del sistema

### Backend
- PHP 8.1 o superior.
- Composer 2.
- Extensiones PHP habituales para Laravel: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer y XML.
- Servidor de base de datos MySQL/MariaDB compatible con InnoDB.

Dependencias relevantes declaradas en `composer.json`:
- `laravel/framework` ^10
- `livewire/livewire` ^3.4 y `livewire/volt` ^1.0
- `spatie/laravel-permission` ^6.21
- `laravel/sanctum`, `laravel/tinker`, `guzzlehttp/guzzle`

### Frontend
- Node.js 18 o superior.
- npm 9 o superior.

Dependencias de compilación front en `package.json`:
- `vite` ^4, `laravel-vite-plugin`, `tailwindcss` ^3, `@tailwindcss/forms`, `autoprefixer`, `axios`.

## Puesta en marcha
1. Tras clonar el repositorio, instala las dependencias de PHP y JavaScript:
   ```bash
   composer install
   # para entornos CI/CD o instalaciones reproducibles usa:
   npm ci
   # en desarrollo interactivo se admite igualmente:
   npm install
   ```
2. Copiar el archivo de entorno y generar la clave de la aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configurar la base de datos en `.env` (ver sección siguiente) y crear un esquema vacío.
4. Ejecutar las migraciones y sembrar los permisos base:
   ```bash
   php artisan migrate
   php artisan db:seed --class=RolesAndPermissionsSeeder
   php artisan permission:cache-reset
   ```
   > El `DatabaseSeeder` no ejecuta seeders automáticos; trabaja exclusivamente con datos reales y lanza de forma manual los procesos que necesites.
5. Construir y levantar los servicios durante el desarrollo:
   ```bash
   npm run dev
   php artisan serve
   ```
   Para compilaciones listas para producción utiliza `npm run build`.

## Verificación de scripts front-end
- Compilación de producción: `npm run build`.
- Servidor de desarrollo (Vite): `npm run dev`.
  - Este comando debe ejecutarse sin errores antes de abrir un Pull Request.

## Configuración de entorno (`.env`)
- **APP_NAME / APP_URL**: nombre y URL pública que se mostrará en la interfaz.
- **APP_ENV / APP_DEBUG**: establece `production` y `false` respectivamente al desplegar.
- **DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD**: credenciales de la base de datos MySQL/MariaDB.
- **QUEUE_CONNECTION**: por defecto es `sync`; cambia a `database` o `redis` si vas a procesar colas fuera de línea.
- **FILESYSTEM_DISK**: define dónde se almacenarán archivos subidos; usa `public` y ejecuta `php artisan storage:link` si necesitas enlaces simbólicos.
- **MAIL_MAILER** y campos asociados: necesarios si se enviarán notificaciones por correo.

Recuerda limpiar la caché de configuración tras cambios significativos:
```bash
php artisan config:cache
php artisan route:cache
```

## Migraciones y estructura de datos
Las migraciones relevantes se encuentran en `database/migrations`:
- `2025_08_26_171700_create_tickets_table.php`: crea la tabla principal de tickets.
- `2025_08_27_091310_create_ticket_logs_table.php`: almacena el historial de cambios por ticket.
- `2025_08_26_170633_create_permission_tables.php`: tablas requeridas por Spatie Permission.
- `2025_10_01_213840_add_is_active_to_users_table.php`: indicador de usuarios activos.

Seeders disponibles:
- `database/seeders/RolesAndPermissionsSeeder.php`: crea los permisos `tickets.*` y `admin.usuarios`, además de los roles (`operaciones`, `supervisor_control`, `coordinador_ti`, `validador`, `gerencia`, `admin`).

Si importas una copia de datos reales asegúrate de ejecutar `php artisan permission:cache-reset` para que los roles aplicados se reflejen de inmediato.

## Operación con datos reales
1. Configura `APP_ENV=production` y `APP_DEBUG=false`.
2. Realiza un respaldo antes de correr migraciones en un entorno con datos sensibles.
3. Ejecuta `php artisan migrate --force` seguido de `php artisan db:seed --class=RolesAndPermissionsSeeder --force` para garantizar que los permisos sigan vigentes.
4. Asigna roles a los usuarios mediante el módulo de administración (`/admin/users`) o con Tinker:
   ```bash
   php artisan tinker
   >>> $user = App\Models\User::find(1);
   >>> $user->syncRoles(['admin']);
   ```
5. Para sincronizar tickets externos respeta los campos definidos en `App\Models\Ticket` y registra sus cambios en `App\Models\TicketLog`.

## Referencias funcionales
- **Rutas**: `routes/web.php` organiza el acceso a los paneles de administración y tickets, aplicando el middleware `role:admin` para las rutas de usuarios y `auth`/`verified` para las vistas de tablero y finalizados.
- **Módulos Livewire de tickets** (`app/Livewire/Tickets`):
  - `Form.php` valida y crea nuevos tickets, notificando al tablero mediante eventos Livewire.
  - `Board.php` muestra tickets activos, permite cambiar estados, reasignar responsables y registra los eventos en `TicketLog`.
  - `Finalizados.php` lista los tickets completados con filtros por prioridad y buscador.
- **Permisos Spatie**: además del seeder, los middlewares `role`, `permission` y `role_or_permission` se registran en `app/Http/Kernel.php`; la configuración completa está en `config/permission.php`.

## Reglas de negocio para los estados de tickets
Los estados disponibles se definen en `App\Models\Ticket::ESTADOS` y siguen este flujo:

1. **pendiente** → el ticket acaba de crearse y está a la espera de ser atendido.
2. **en_proceso** → el responsable está trabajando activamente en la solicitud. El primer comentario que se registre en el tablero cambia automáticamente el estado de pendiente a en proceso.
3. **finalizado** → el ticket se cierra con un comentario que documenta la resolución. Mientras el ticket esté en proceso se pueden registrar comentarios adicionales sin modificar el estado.

El tablero (`App\Livewire\Tickets\Board`) muestra únicamente los estados activos (`pendiente`, `en_proceso`). El resumen superior incluye el total de finalizados y la vista `tickets.finalizados` permite revisar el detalle histórico.

## Comandos útiles
- Ejecutar pruebas: `php artisan test`
- Ejecutar Pint (estilo): `./vendor/bin/pint`
- Limpiar y reconstruir cachés: `php artisan optimize:clear && php artisan optimize`

Con estos pasos podrás levantar la aplicación en un entorno local o productivo y mantener consistentes los permisos y procesos relacionados con la gestión de tickets.
