# Informe de integración de API REST

## Pastelería La María

**Guía de referencia:** `Guia_Integracion_API_REST_Pasteleria_La_Maria.md`  
**Proyecto:** `app_lamaria`  
**Fecha:** 8 de noviembre de 2026

## 1. Hallazgos iniciales

- El proyecto es una aplicación Laravel 12 conectada a MariaDB mediante la base de datos `db_lamaria`.
- La base de datos contiene las tablas `productos` y `categorias`, con los campos y relaciones utilizados por los modelos: `id_producto`, `id_categoria`, `nombre`, `descripcion`, `precio`, `image_url` y `activa`. El modelo `Producto` ya tenía la relación `categoria`.
- Ya existían las rutas `GET /api/productos` y `GET /api/categorias`, los controladores API correspondientes y su registro desde `bootstrap/app.php`.
- Había 12 productos activos y 7 categorías activas. Los 12 productos tenían imagen; el enlace `public/storage` existía y los archivos estaban en el disco público.
- No había un catálogo estático dentro de la carpeta del proyecto. El archivo `C:\Users\luisf\index.html`, fuera de Laravel, solo contenía «Hola mundo» y no se modificó.
- Tampoco existía `main.js`. El archivo `public/js/app.js` pertenece al panel administrativo y controla su menú lateral; no era el archivo del catálogo.
- El repositorio Git detectado está en `C:\Users\luisf`, no en la carpeta del proyecto, y no tiene commits. No se pudo usar un historial Git fiable para determinar quién hizo cambios anteriores.

## 2. Avances que ya estaban realizados correctamente

- Los modelos `Producto` y `Categoria` usaban las tablas y claves primarias existentes.
- `Producto` tenía configurada la relación `categoria`.
- Los controladores API consultaban únicamente productos y categorías activos. El endpoint de productos también excluía productos asociados a categorías inactivas.
- Los dos endpoints estaban declarados en `routes/api.php` y Laravel los registraba.
- El almacenamiento público ya estaba enlazado y había imágenes de productos disponibles.
- El panel de administración ya permitía gestionar productos, categorías y la información empresarial, incluido el número de WhatsApp.

## 3. Errores y configuraciones corregidos

- La configuración CORS contenía los orígenes permitidos, pero no los demás parámetros previstos por la guía. Se completaron métodos, encabezados, patrones, duración de caché y soporte de credenciales.
- El controlador de productos construía las URL de imágenes con `asset()`, que podía usar `APP_URL=http://localhost` aunque la aplicación se abriera mediante `127.0.0.1`. Se ajustó la generación de URL para utilizar el host de la solicitud.
- `public/js/app.js` contenía constantes de las API que no se utilizaban en el panel. Se eliminaron sin modificar el comportamiento de su menú.
- La prueba de inicio existente esperaba una respuesta satisfactoria, pero no preparaba la tabla `empresa`, que ahora se consulta al servir el catálogo. Se actualizó para preparar la base y comprobar la página.
- Las migraciones de `rol`, `rol_usuario` y `empresa` aparecen pendientes aunque esas tablas ya existen en MariaDB. No se ejecutaron migraciones en esa base para evitar conflictos o alterar datos existentes.

## 4. Archivos modificados

| Archivo | Cambio |
|---|---|
| `bootstrap/app.php` | Se ordenó la indentación del registro de rutas, que ya incluía `routes/api.php`. |
| `config/cors.php` | Se completaron los parámetros CORS para los orígenes configurados. |
| `routes/web.php` | La ruta `/` ahora muestra el catálogo y lee el WhatsApp desde Empresa. |
| `app/Http/Controllers/Api/ProductoApiController.php` | Se ajustó la generación de URL pública para imágenes. |
| `public/js/app.js` | Se retiraron constantes API sin uso; se conservó el control del menú. |
| `resources/views/catalogo.blade.php` | Se creó la vista pública con filtros, estado de carga y contenedor del catálogo. |
| `public/css/catalogo.css` | Se añadieron estilos responsivos para el catálogo. |
| `public/js/catalogo.js` | Se añadieron las consultas `fetch()`, tarjetas y filtros dinámicos, recarga y acciones por WhatsApp y cotización. |
| `tests/Feature/CatalogApiTest.php` | Se añadieron pruebas de categorías/productos activos, relación, imágenes y CORS. |
| `tests/Feature/ExampleTest.php` | Se actualizó la prueba de inicio para verificar el catálogo. |
| `README.md` | Se documentaron el catálogo, sus endpoints y la precaución con las migraciones existentes. |

No se modificaron los controladores de categorías ni se duplicaron las rutas o controladores API, porque ya existían.

## 5. Nombres de archivos HTML y JavaScript

- **`index.html`:** no existe como catálogo dentro de `app_lamaria`. El único localizado estaba fuera del proyecto y no era el sitio de Pastelería La María, por lo que no se utilizó.
- **`main.js`:** no existe dentro del proyecto.
- **Archivos usados:** el catálogo se implementó en `resources/views/catalogo.blade.php`, con JavaScript en `public/js/catalogo.js` y estilos en `public/css/catalogo.css`.
- **`public/js/app.js`:** se conservó para el menú del panel administrativo; no se reutilizó para el catálogo.

## 6. Etapas de la actividad completadas

1. **Preparación de rutas API:** comprobado el registro de `routes/api.php`.
2. **Endpoint de productos:** conservado y verificado `GET /api/productos`.
3. **Controlador de productos:** adaptado a los nombres reales de modelos, campos y relación.
4. **Fotografías:** confirmado el enlace `public/storage`; se corrigió la URL pública devuelta por la API.
5. **Prueba del endpoint de productos:** comprobada la respuesta JSON real de MariaDB.
6. **CORS:** completada la configuración y probada con un origen permitido de Live Server.
7. **Consumo de la API con JavaScript:** implementadas las consultas y el renderizado dinámico.
8. **Catálogo HTML:** creada la vista Blade, ya que el HTML estático de la guía no estaba en el proyecto.
9. **Filtros de categorías:** implementada la carga y selección dinámica de categorías activas.
10. **Prueba integral:** comprobados endpoints, catálogo, imágenes, filtros y acciones.

El catálogo no se actualiza en tiempo real; ofrece el botón «Actualizar catálogo» para volver a consultar las API, de acuerdo con el alcance de la guía.

## 7. Pruebas ejecutadas y resultados

- `php artisan test`: **16 pruebas aprobadas, 77 aserciones**.
- `php artisan route:list --path=api`: registró `GET api/productos` y `GET api/categorias`.
- Verificación HTTP de los endpoints contra la base activa:
  - `/api/productos`: HTTP 200, respuesta exitosa con 12 productos.
  - `/api/categorias`: HTTP 200, respuesta exitosa con 7 categorías.
- Verificación de la página principal: HTTP 200; la vista incluyó el catálogo y cargó `public/js/catalogo.js`.
- Verificación en navegador:
  - Se generaron 12 tarjetas y 7 filtros de categorías.
  - El filtro «Helado» mostró 11 productos; «Todos» mostró 12.
  - Se generaron 12 enlaces de WhatsApp y 12 de solicitud de cotización.
  - Las 12 imágenes se decodificaron correctamente.
- CORS permitió el origen `http://localhost:5500`.
- `php -l` no reportó errores en los archivos PHP revisados.
- `php artisan config:clear` se ejecutó correctamente.
- No se ejecutó `npm run build`: Node.js no está disponible en este entorno. El JavaScript del catálogo sí se probó en el navegador.
- El servidor local temporal usado para las pruebas se detuvo al terminar.

## 8. Evidencias que faltan para entregar

La guía solicita reunir capturas propias en un documento PDF o Word. Debes capturar:

1. **Configuración de Laravel:** `bootstrap/app.php` mostrando el registro de rutas API.
2. **Controladores y rutas:** `routes/api.php`, `ProductoApiController.php` y `CategoriaApiController.php`.
3. **Respuestas JSON:** `/api/productos` y `/api/categorias` abiertos en el navegador.
4. **Consumo de API:** `public/js/catalogo.js`, mostrando `fetch()`, procesamiento JSON y creación de tarjetas.
5. **Catálogo:** la página pública con productos, imágenes, descripciones y precios.
6. **Filtros:** botones de categorías y resultados al seleccionar filtros distintos.
7. **Prueba de integración:** capturas de una categoría y un producto creados desde el administrador y del catálogo actualizado donde aparezcan ambos.

También falta reunir esas capturas en el documento de entrega y redactar las ocho respuestas de reflexión incluidas en la guía.

## 9. Precaución con la base de datos

En MariaDB existen las tablas `rol`, `rol_usuario` y `empresa`, pero las migraciones correspondientes aparecen pendientes. Además, `productos` y `categorias` no están definidas en las migraciones del proyecto. No se ejecutó `php artisan migrate` sobre la base existente.

Antes de ejecutar migraciones en esa base, respáldala y concilia el estado de las tablas con el registro de migraciones. La API y las pruebas sí fueron verificadas sin aplicar cambios de esquema en la base de datos de trabajo.
