# P3 Global

Este proyecto se basa en Laravel y proporciona las bases para la plataforma P3 Global. A continuación se describe el flujo recomendado para su configuración local y la operación diaria relacionada con la creación de usuarios y la carga de datos iniciales.

## Configuración rápida

1. Instala las dependencias de PHP y JavaScript:
   ```bash
   composer install
   npm install
   ```
2. Copia el archivo de entorno y actualiza las variables necesarias:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Ejecuta las migraciones de base de datos:
   ```bash
   php artisan migrate
   ```
4. Compila los assets cuando sea necesario:
   ```bash
   npm run dev
   ```

## Creación de usuarios reales

Los usuarios finales deben crearse siempre mediante los flujos oficiales del sistema:

- **Módulo de administración:** ingresa con una cuenta con privilegios de administración y utiliza el formulario de creación/gestión de usuarios para dar de alta nuevas personas.
- **Importaciones controladas:** cuando se requiera incorporar lotes de usuarios (por ejemplo, provenientes de un padrón oficial), solicita al equipo de operaciones que ejecute el proceso de importación aprobado. Dicho proceso valida la procedencia de la información antes de insertarla en la base de datos.

> **Importante:** No utilices seeders automáticos para generar usuarios reales. El seeder `UserSeeder` fue retirado para evitar datos ficticios en entornos productivos.

## Datos iniciales

Si es necesario poblar información de referencia (catálogos, listados oficiales, etc.), sigue el procedimiento descrito en [`docs/manual-data-import.md`](docs/manual-data-import.md). Ese documento detalla cómo obtener los archivos oficiales, validarlos y cargarlos manualmente mediante herramientas administrativas.

Cualquier actualización a los datos iniciales debe quedar registrada en el documento anterior, junto con la fuente oficial utilizada.

## Contribuciones

Las solicitudes de cambios se revisan mediante _pull requests_. Asegúrate de incluir pruebas y documentación cuando corresponda.

## Licencia

Este proyecto se distribuye bajo la licencia MIT.
