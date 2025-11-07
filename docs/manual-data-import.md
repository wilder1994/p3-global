# Procedimiento para carga manual de datos iniciales

Este documento reemplaza cualquier _seeder_ automático para poblar información base. Sigue estos pasos para garantizar que los datos provengan de fuentes confiables y queden correctamente auditados.

## 1. Identificar la fuente oficial

1. Define el catálogo o conjunto de datos que se requiere (por ejemplo, listado de países, instituciones asociadas, etc.).
2. Ubica la fuente oficial correspondiente (sitio gubernamental, dependencia regulatoria o repositorio institucional) y descarga el archivo más reciente. Documenta la URL y la fecha de descarga.

## 2. Validar el archivo

1. Revisa la integridad del archivo (formato, número de columnas, codificación).
2. Verifica que el contenido coincida con la publicación oficial (comparando totales, fechas de vigencia, folios, etc.).
3. Guarda el archivo en un repositorio seguro con control de versiones o en el almacenamiento aprobado por el área de operaciones.

## 3. Preparar la carga

1. Normaliza los encabezados para que coincidan con la estructura de las tablas internas (por ejemplo, usando una hoja de cálculo o un script controlado por operaciones).
2. Si se requiere transformación (limpieza de caracteres especiales, mapeo de códigos, etc.), documenta cada paso aplicado y conserva los scripts utilizados.

## 4. Importar mediante herramientas administrativas

1. Inicia sesión con un usuario con permisos de administración.
2. Dirígete al módulo correspondiente (por ejemplo, «Catálogos» → «Países»).
3. Usa la función de importación/mantenimiento disponible. Si no existe, coordina con el equipo de desarrollo para ejecutar una carga controlada (script temporal) en un entorno supervisado.
4. Registra en el acta de operaciones la fecha de carga, el responsable, la fuente de los datos y el hash del archivo importado.

## 5. Auditoría posterior

1. Realiza consultas de verificación para confirmar el número de registros y campos clave.
2. Conserva los respaldos y evidencia de la carga (archivos originales, transformaciones, reportes de importación).
3. En caso de detectar inconsistencias, revierte la carga usando los respaldos generados y vuelve a iniciar el procedimiento desde el paso 1.

## Registro de cargas

Completa la siguiente tabla cada vez que se ejecute una carga inicial o actualización:

| Fecha | Responsable | Catálogo / Dataset | Fuente oficial | URL o repositorio | Hash (SHA256) | Observaciones |
|-------|-------------|--------------------|----------------|-------------------|---------------|---------------|
|       |             |                    |                |                   |               |               |

Mantén este documento actualizado para que cualquier miembro del equipo pueda seguir el proceso sin recurrir a _seeders_ automáticos.
