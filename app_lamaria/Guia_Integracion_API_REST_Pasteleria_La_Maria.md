# Guía completa: Integración de una API REST para el catálogo de Pastelería La María

> **Objetivo:** conectar la aplicación administrativa desarrollada en
> Laravel con la página web estática para que los productos y categorías
> almacenados en MariaDB se muestren dinámicamente en el catálogo
> público.
>
> **Importante:** esta práctica parte de que los dos proyectos ya
> existen y funcionan por separado. No se requiere crear de nuevo la
> base de datos ni los módulos administrativos.

------------------------------------------------------------------------

## 1. ¿Qué se va a construir?

El flujo de información será:

1.  El administrador registra un producto desde Laravel.
2.  Laravel guarda el producto en MariaDB.
3.  El cliente abre el catálogo público.
4.  JavaScript solicita los productos a la API REST de Laravel.
5.  Laravel consulta los productos y sus categorías activas.
6.  Laravel devuelve los datos en formato JSON.
7.  JavaScript interpreta el JSON y crea las tarjetas del catálogo.
8.  El catálogo muestra fotografías, nombres, descripciones, precios y
    categorías.
9.  Los filtros se generan con las categorías activas registradas en la
    base de datos.

**Resultado esperado:** al registrar un producto o categoría desde el
administrador, el catálogo podrá mostrarlo después de volver a consultar
la API y actualizar la página. Esta práctica no implementa actualización
en tiempo real.

## 2. Requisitos previos

Antes de comenzar, comprueba que tienes:

-   [ ] Proyecto administrativo Laravel `app_la-maria`.
-   [ ] PHP instalado.
-   [ ] XAMPP y MariaDB funcionando.
-   [ ] Base de datos `db_lamaria`.
-   [ ] Tablas `productos` y `categorias` con registros.
-   [ ] Relación entre productos y categorías.
-   [ ] Fotografías de productos almacenadas en Laravel.
-   [ ] Página web estática de Pastelería La María.
-   [ ] Visual Studio Code.
-   [ ] Navegador actualizado.

### Estructura que se utilizará

En Laravel se trabajará principalmente con:

``` text
app_la-maria/
├── app/
│   └── Http/
│       └── Controllers/
│           └── Api/
│               ├── ProductoApiController.php
│               └── CategoriaApiController.php
├── bootstrap/
│   └── app.php
├── config/
│   └── cors.php
├── routes/
│   ├── web.php
│   └── api.php
└── storage/
    └── app/
        └── public/
            └── productos/
```

En la página estática se modificarán:

``` text
proyecto-web/
├── index.html
└── js/
    └── main.js
```

Los nombres de carpetas pueden variar en tu proyecto; conserva la
estructura real que ya tienes.

------------------------------------------------------------------------

# PARTE A. Configurar Laravel para la API REST

## 3. Etapa 1: preparar las rutas API

### Paso 1. Abrir el proyecto

Abre Visual Studio Code y carga la carpeta `app_la-maria`.

### Paso 2. Revisar `bootstrap/app.php`

Abre `bootstrap/app.php`. Localiza `withRouting()` y verifica que
registre el archivo API:

``` php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

Conserva las demás configuraciones que ya tenga tu archivo. No
reemplaces todo `bootstrap/app.php` sin revisar su contenido.

### Paso 3. Crear `routes/api.php`

Dentro de `routes`, crea el archivo `api.php` si todavía no existe.

La carpeta debe contener, entre otros, estos archivos:

``` text
routes/
├── web.php
├── api.php
└── console.php
```

-   `web.php`: conserva las rutas de vistas y funcionalidades
    administrativas.
-   `api.php`: contiene las rutas que entregan información a otras
    aplicaciones.

------------------------------------------------------------------------

## 4. Etapa 2: crear el endpoint de productos

Abre `routes/api.php` y agrega:

``` php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;

// Consultar productos activos
Route::get('/productos', [ProductoApiController::class, 'index']);
```

### ¿Qué significa?

-   `Route::get()`: define una ruta para consultar información mediante
    HTTP GET.
-   `/productos`: identifica el recurso.
-   `ProductoApiController`: controlador que procesa la solicitud.
-   `index`: método que se ejecutará.

El endpoint será:

``` text
http://127.0.0.1:8000/api/productos
```

------------------------------------------------------------------------

## 5. Etapa 3: crear el controlador de productos

### Paso 1. Generar el controlador

Abre PowerShell o la terminal de VS Code en la carpeta raíz de Laravel y
ejecuta:

``` bash
php artisan make:controller Api/ProductoApiController
```

Se creará:

``` text
app/Http/Controllers/Api/ProductoApiController.php
```

### Paso 2. Incorporar el código

Coloca el siguiente contenido en el controlador:

``` php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;

class ProductoApiController extends Controller
{
    /**
     * Consultar productos activos y sus imágenes.
     */
    public function index(): JsonResponse
    {
        $productos = Producto::with('categoria')
            ->where('activa', true)
            ->whereHas('categoria', function ($query) {
                $query->where('activa', true);
            })
            ->orderBy('nombre')
            ->get();

        $datos = $productos->map(function ($producto) {
            return [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio' => $producto->precio,
                'categoria' => $producto->categoria->nombre,
                'imagen' => $producto->image_url
                    ? asset('storage/' . $producto->image_url)
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $datos->count(),
            'data' => $datos,
        ], 200);
    }
}
```

### Paso 3. Comprender el código

  -----------------------------------------------------------------------
  Instrucción                         Función
  ----------------------------------- -----------------------------------
  `Producto::with('categoria')`       Recupera productos y su categoría
                                      relacionada.

  `where('activa', true)`             Consulta productos activos.

  `whereHas('categoria', ...)`        Considera productos cuya categoría
                                      esté activa.

  `orderBy('nombre')`                 Ordena alfabéticamente.

  `get()`                             Ejecuta la consulta.

  `map()`                             Organiza cada registro en una
                                      estructura de datos.

  `response()->json()`                Devuelve la respuesta en formato
                                      JSON.
  -----------------------------------------------------------------------

El controlador funciona como intermediario entre MariaDB y la página
web. Consulta y organiza la información; no modifica los registros.

> **Revisión importante antes de continuar:** el código de la actividad
> presupone que el modelo `Producto` tiene una relación llamada
> `categoria`, que existen los campos `activa`, `id_producto`, `nombre`,
> `descripcion`, `precio` e `image_url`, y que la relación está
> configurada correctamente. Si tu proyecto usa otros nombres, adapta el
> código a tu estructura real. No cambies nombres de columnas a ciegas.

------------------------------------------------------------------------

## 6. Etapa 4: preparar las fotografías

### Paso 1. Crear el enlace de almacenamiento

Desde la raíz del proyecto Laravel ejecuta:

``` bash
php artisan storage:link
```

Este comando crea el enlace público `public/storage`, que permite
acceder a los archivos guardados en `storage/app/public`.

Ejecuta el comando solamente si el enlace todavía no existe o necesitas
reconstruirlo.

### Paso 2. Revisar la ubicación de las imágenes

Ejemplo de archivo:

``` text
storage/app/public/productos/pastel.jpg
```

Su dirección pública local sería:

``` text
http://127.0.0.1:8000/storage/productos/pastel.jpg
```

Comprueba que las rutas guardadas en los registros correspondan con la
ubicación real de las imágenes.

------------------------------------------------------------------------

## 7. Etapa 5: probar el endpoint de productos

### Paso 1. Iniciar Laravel

En la terminal, dentro del proyecto, ejecuta:

``` bash
php artisan serve
```

La aplicación estará disponible normalmente en:

``` text
http://127.0.0.1:8000
```

Deja esa terminal abierta mientras haces las pruebas.

### Paso 2. Comprobar las rutas API

Abre otra terminal en el proyecto y ejecuta:

``` bash
php artisan route:list --path=api
```

Debes identificar:

``` text
GET  api/productos
```

### Paso 3. Abrir el endpoint

En el navegador visita:

``` text
http://127.0.0.1:8000/api/productos
```

La respuesta tendrá una estructura similar a esta (los datos son
ilustrativos):

``` json
{
  "success": true,
  "total": 2,
  "data": [
    {
      "id_producto": 1,
      "nombre": "Pastel de chocolate",
      "descripcion": "Pastel para eventos",
      "precio": "350.00",
      "categoria": "Pasteles",
      "imagen": "http://127.0.0.1:8000/storage/productos/pastel.jpg"
    }
  ]
}
```

Si aparece una respuesta JSON, significa que el endpoint está
respondiendo. Comprueba también que los datos sean los esperados y que
las imágenes tengan direcciones válidas.

------------------------------------------------------------------------

## 8. Etapa 6: configurar CORS

CORS (Cross-Origin Resource Sharing) permite controlar qué orígenes
pueden realizar solicitudes a un servidor.

En esta práctica las aplicaciones pueden ejecutarse en direcciones
diferentes, por ejemplo:

  Aplicación        Dirección
  ----------------- -------------------------
  Página estática   `http://localhost:5500`
  Laravel           `http://127.0.0.1:8000`

Aunque ambas se ejecuten en el mismo equipo, el navegador las considera
orígenes diferentes.

### Paso 1. Publicar la configuración si hace falta

Si `config/cors.php` no existe, ejecuta:

``` bash
php artisan config:publish cors
```

### Paso 2. Configurar `config/cors.php`

La configuración de la actividad es:

``` php
<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost',
        'http://localhost:5500',
        'http://127.0.0.1:5500',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
```

Los orígenes autorizados deben coincidir exactamente con la dirección y
el puerto desde los que abres la página estática. Si usas otro puerto,
agrega ese origen.

### Paso 3. Limpiar la configuración

Ejecuta:

``` bash
php artisan config:clear
```

Esto permite que Laravel vuelva a cargar la configuración actualizada.

------------------------------------------------------------------------

# PARTE B. Conectar la página estática con Laravel

## 9. Etapa 7: consumir la API desde JavaScript

Abre el proyecto de la página estática en Visual Studio Code y localiza
`js/main.js`.

### Paso 1. Declarar la dirección de la API

Al inicio de `main.js`, agrega:

``` javascript
const API_PRODUCTOS = 'http://127.0.0.1:8000/api/productos';
```

### Paso 2. Crear la función para consultar productos

Agrega:

``` javascript
async function cargarProductos() {
    const catalogo = document.getElementById('catalogo');

    try {
        catalogo.innerHTML = `
            <div class="col-12 text-center">
                <p>Cargando productos...</p>
            </div>
        `;

        const respuesta = await fetch(API_PRODUCTOS);

        if (!respuesta.ok) {
            throw new Error('No fue posible consultar los productos.');
        }

        const resultado = await respuesta.json();

        if (!resultado.success) {
            throw new Error('La API no pudo recuperar los productos.');
        }

        console.log('Productos recibidos:', resultado.data);

        mostrarProductos(resultado.data);

    } catch (error) {
        console.error('Error:', error);

        catalogo.innerHTML = `
            <div class="col-12 text-center">
                <p>No fue posible cargar el catálogo.</p>
                <p>Intenta nuevamente más tarde.</p>
            </div>
        `;
    }
}
```

**Qué hace:** muestra un mensaje de carga, realiza la solicitud con
`fetch()`, convierte la respuesta a JSON y llama a `mostrarProductos()`.
Si ocurre un error, presenta un mensaje en el catálogo y lo registra en
la consola.

### Paso 3. Crear las tarjetas dinámicamente

En el mismo `main.js`, agrega:

``` javascript
function mostrarProductos(productos) {
    const catalogo = document.getElementById('catalogo');

    catalogo.innerHTML = '';

    if (!productos || productos.length === 0) {
        catalogo.textContent = 'No hay productos disponibles.';
        return;
    }

    productos.forEach(producto => {
        const columna = document.createElement('div');

        columna.className = 'col-md-4 producto';

        // Identificar la categoría
        columna.dataset.categoria =
            normalizarCategoria(producto.categoria);

        // Crear tarjeta
        const tarjeta = document.createElement('div');
        tarjeta.className = 'card h-100';

        // Crear imagen
        const imagen = document.createElement('img');
        imagen.className = 'card-img-top';
        imagen.alt = producto.nombre;

        if (producto.imagen) {
            imagen.src = producto.imagen;

            imagen.onerror = function () {
                this.onerror = null;
                this.alt = 'Imagen no disponible';
                this.removeAttribute('src');
            };
        } else {
            imagen.alt = 'Imagen no disponible';
        }

        // Contenido
        const cuerpo = document.createElement('div');
        cuerpo.className = 'card-body';

        // Nombre
        const nombre = document.createElement('h3');
        nombre.className = 'h5';
        nombre.textContent = producto.nombre;

        // Categoría
        const categoria = document.createElement('p');
        categoria.className = 'small text-muted';
        categoria.textContent = producto.categoria;

        // Descripción
        const descripcion = document.createElement('p');
        descripcion.textContent = producto.descripcion;

        // Precio
        const precio = document.createElement('p');
        precio.className = 'fw-bold';

        precio.textContent =
            producto.precio !== null &&
            producto.precio !== ''
                ? `$${Number(producto.precio).toFixed(2)} MXN`
                : 'Consultar precio';

        // Botón WhatsApp
        const whatsapp = document.createElement('button');
        whatsapp.className = 'btn btn-success btn-sm btn-wapp';
        whatsapp.textContent = 'Solicitar por WhatsApp';
        whatsapp.dataset.producto = producto.nombre;

        // Botón Cotizar
        const cotizar = document.createElement('button');
        cotizar.className = 'btn btn-outline-primary btn-sm btn-cotizacion';
        cotizar.textContent = 'Cotizar';
        cotizar.dataset.producto = producto.nombre;

        // Integrar elementos
        cuerpo.append(
            nombre,
            categoria,
            descripcion,
            precio,
            whatsapp,
            cotizar
        );

        tarjeta.append(imagen, cuerpo);
        columna.appendChild(tarjeta);
        catalogo.appendChild(columna);
    });
}
```

**Qué hace:** recorre los productos recibidos y crea una tarjeta para
cada uno. Inserta nombre, categoría, descripción, precio, fotografía y
botones.

Se usa `textContent` para insertar los datos como texto y no
interpretarlos como código HTML.

> **Atención con los botones:** el código conserva las clases y los
> datos de producto para WhatsApp y cotización, pero los manejadores de
> eventos existentes deben conservarse o adaptarse para que también
> funcionen con botones creados dinámicamente. Comprueba esta
> funcionalidad en tu proyecto.

### Paso 4. Ejecutar la consulta al cargar la página

Agrega al final de `main.js`:

``` javascript
document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();
});
```

Esto inicia la consulta cuando el documento HTML ya se ha cargado.

------------------------------------------------------------------------

## 10. Etapa 8: modificar el catálogo HTML

Abre `index.html` y localiza el contenedor donde aparecen los productos.

### Paso 1. Identificar el contenedor

Debe existir un elemento con el identificador `catalogo`, por ejemplo:

``` html
<div class="row g-4" id="catalogo">
    <!-- Productos escritos manualmente -->
</div>
```

### Paso 2. Sustituir las tarjetas manuales

Reemplaza únicamente el contenido del contenedor por:

``` html
<div class="row g-4" id="catalogo">
    <div class="col-12 text-center">
        <p>Cargando productos...</p>
    </div>
</div>
```

No elimines el encabezado, los filtros ni las demás secciones de la
página.

### Paso 3. Revisar la referencia a JavaScript

Al final de `index.html`, verifica que exista:

``` html
<script src="js/main.js" defer></script>
```

El atributo `defer` permite ejecutar el archivo después de analizar el
documento HTML.

### Paso 4. Probar la integración

1.  Guarda los archivos.
2.  En Laravel ejecuta `php artisan optimize:clear`.
3.  Mantén activo `php artisan serve`.
4.  Abre la página estática mediante Live Server o Apache.
5.  Actualiza con `Ctrl + F5`.
6.  Comprueba que aparezcan los productos y sus fotografías.

------------------------------------------------------------------------

# PARTE C. Crear filtros dinámicos

## 11. Etapa 9: integrar los filtros de categorías

El objetivo es que los botones de filtro se generen con las categorías
activas registradas en MariaDB.

### Paso 1. Crear el controlador de categorías

En la terminal de Laravel ejecuta:

``` bash
php artisan make:controller Api/CategoriaApiController
```

Se creará:

``` text
app/Http/Controllers/Api/CategoriaApiController.php
```

### Paso 2. Incorporar el código

``` php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;

class CategoriaApiController extends Controller
{
    /**
     * Consultar categorías activas.
     */
    public function index(): JsonResponse
    {
        $categorias = Categoria::where('activa', true)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $categorias->count(),
            'data' => $categorias->map(function ($categoria) {
                return [
                    'id_categoria' => $categoria->id_categoria,
                    'nombre' => $categoria->nombre,
                ];
            }),
        ], 200);
    }
}
```

Este controlador recupera únicamente las categorías activas y devuelve
su identificador y nombre en formato JSON.

### Paso 3. Crear la ruta de categorías

Abre `routes/api.php`. Agrega la importación:

``` php
use App\Http\Controllers\Api\CategoriaApiController;
```

Y agrega la ruta:

``` php
Route::get('/categorias', [CategoriaApiController::class, 'index']);
```

Conserva también la importación y la ruta de productos. El archivo
tendrá ambas rutas:

``` php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\CategoriaApiController;

Route::get('/productos', [ProductoApiController::class, 'index']);
Route::get('/categorias', [CategoriaApiController::class, 'index']);
```

### Paso 4. Comprobar el endpoint de categorías

Ejecuta:

``` bash
php artisan route:list --path=api
```

Debes encontrar:

``` text
GET|HEAD  api/productos
GET|HEAD  api/categorias
```

Abre en el navegador:

``` text
http://127.0.0.1:8000/api/categorias
```

La respuesta será similar a esta (los datos son ilustrativos):

``` json
{
    "success": true,
    "total": 3,
    "data": [
        {
            "id_categoria": 1,
            "nombre": "Pasteles"
        },
        {
            "id_categoria": 2,
            "nombre": "Cupcakes"
        },
        {
            "id_categoria": 3,
            "nombre": "Postres"
        }
    ]
}
```

### Paso 5. Modificar los filtros de `index.html`

Localiza el bloque de botones de categorías y sustitúyelo por:

``` html
<div class="text-center mb-4" id="filtros">
    <button
        class="btn btn-sm btn-outline-primary m-1 filtro active"
        data-filtro="todos">
        Todos
    </button>
</div>
```

Se conserva el botón **Todos**. Los demás botones serán generados por
JavaScript.

### Paso 6. Declarar la dirección de la API de categorías

Al inicio de `main.js`, junto a la dirección de productos, agrega:

``` javascript
const API_CATEGORIAS = 'http://127.0.0.1:8000/api/categorias';
```

Las dos constantes serán:

``` javascript
const API_PRODUCTOS = 'http://127.0.0.1:8000/api/productos';
const API_CATEGORIAS = 'http://127.0.0.1:8000/api/categorias';
```

### Paso 7. Crear la función para cargar categorías

Agrega en `main.js`:

``` javascript
async function cargarCategorias() {
    const filtros = document.getElementById('filtros');

    try {
        const respuesta = await fetch(API_CATEGORIAS);

        if (!respuesta.ok) {
            throw new Error('Error al consultar las categorías');
        }

        const resultado = await respuesta.json();

        if (!resultado.success) {
            throw new Error('No fue posible obtener las categorías');
        }

        renderizarFiltros(resultado.data);

    } catch (error) {
        console.error('Error al cargar categorías:', error);

        filtros.innerHTML = `
            <button
                class="btn btn-sm btn-outline-primary m-1 filtro active"
                data-filtro="todos">
                Todos
            </button>
        `;
    }
}
```

### Paso 8. Crear los botones dinámicamente

Agrega:

``` javascript
function renderizarFiltros(categorias) {
    const filtros = document.getElementById('filtros');

    // Conservar el botón Todos
    filtros.innerHTML = `
        <button
            class="btn btn-sm btn-outline-primary m-1 filtro active"
            data-filtro="todos">
            Todos
        </button>
    `;

    // Crear un botón por cada categoría activa
    categorias.forEach(categoria => {
        const boton = document.createElement('button');

        boton.className = 'btn btn-sm btn-outline-primary m-1 filtro';

        boton.dataset.filtro =
            normalizarCategoria(categoria.nombre);

        boton.textContent = categoria.nombre;

        filtros.appendChild(boton);
    });
}
```

### Paso 9. Normalizar los nombres de categorías

Agrega:

``` javascript
function normalizarCategoria(categoria) {
    return categoria
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .replace(/\s+/g, '-');
}
```

La normalización permite comparar nombres aunque contengan espacios,
mayúsculas o acentos.

Ejemplos:

-   `Pasteles` → `pasteles`
-   `Postres especiales` → `postres-especiales`
-   `Repostería` → `reposteria`

### Paso 10. Agregar el funcionamiento de los filtros

Agrega este evento en `main.js`, fuera de las demás funciones:

``` javascript
document.getElementById('filtros').addEventListener(
    'click',
    function (evento) {
        const boton = evento.target.closest('.filtro');

        if (!boton) return;

        const categoriaSeleccionada = boton.dataset.filtro;

        const productos = document.querySelectorAll(
            '#catalogo .producto'
        );

        productos.forEach(producto => {
            const categoriaProducto = producto.dataset.categoria;

            producto.style.display =
                categoriaSeleccionada === 'todos' ||
                categoriaProducto === categoriaSeleccionada
                    ? ''
                    : 'none';
        });

        // Actualizar botón activo
        document.querySelectorAll('#filtros .filtro').forEach(filtro => {
            filtro.classList.remove('active');
        });

        boton.classList.add('active');
    }
);
```

Se utiliza delegación de eventos para que el filtro funcione aunque sus
botones se creen dinámicamente.

### Paso 11. Integrar la carga de productos y categorías

Localiza el evento `DOMContentLoaded` que agregaste anteriormente y
sustitúyelo por:

``` javascript
document.addEventListener('DOMContentLoaded', function () {
    cargarCategorias();
    cargarProductos();
});
```

Verifica que dentro de `mostrarProductos()` exista esta instrucción:

``` javascript
columna.dataset.categoria =
    normalizarCategoria(producto.categoria);
```

Es necesaria para relacionar cada tarjeta con su filtro.

------------------------------------------------------------------------

# PARTE D. Pruebas y entrega

## 12. Etapa 10: comprobar el funcionamiento integral

Realiza estas pruebas y registra el resultado:

  -----------------------------------------------------------------------
  Prueba                  Resultado esperado      ¿Completada?
  ----------------------- ----------------------- -----------------------
  Abrir endpoint de       Se obtiene información  \[ \]
  productos               JSON.                   

  Abrir endpoint de       Se obtienen las         \[ \]
  categorías              categorías activas.     

  Abrir catálogo público  Se muestran los         \[ \]
                          productos registrados.  

  Visualizar fotografías  Se presentan las        \[ \]
                          imágenes                
                          correspondientes.       

  Seleccionar «Todos»     Aparecen todos los      \[ \]
                          productos disponibles.  

  Seleccionar una         Aparecen solo productos \[ \]
  categoría               de esa categoría.       

  Registrar un producto   Aparece al volver a     \[ \]
  nuevo                   actualizar el catálogo. 

  Registrar una categoría Aparece un nuevo botón  \[ \]
  nueva                   de filtro al            
                          actualizar.             

  Desactivar una          Desaparece el filtro y  \[ \]
  categoría               sus productos dejan de  
                          estar disponibles en la 
                          consulta.               

  Cambiar el nombre de    Se actualiza el nombre  \[ \]
  una categoría           del filtro y su         
                          clasificación.          
  -----------------------------------------------------------------------

### Prueba de integración adicional

Desde el módulo administrativo de Pastelería La María:

1.  Registra una categoría nueva, por ejemplo, **Panadería**.
2.  Registra un producto asociado a esa categoría.
3.  Verifica que ambos registros se almacenen correctamente en MariaDB.
4.  Abre nuevamente el catálogo público.
5.  Comprueba que aparezca el producto registrado.
6.  Verifica que se genere automáticamente el filtro **Panadería**.
7.  Selecciona ese filtro y comprueba que únicamente se muestre el
    producto correspondiente.

**Nota:** la actualización se produce al volver a consultar la API. No
es una actualización en tiempo real.

------------------------------------------------------------------------

## 13. Evidencias que debes entregar

El documento final debe ser PDF o Word e incluir estas siete evidencias:

### Evidencia 1. Configuración de Laravel

-   [ ] Captura de `bootstrap/app.php`.
-   [ ] Debe identificarse el registro de las rutas API.

### Evidencia 2. Controladores y rutas

-   [ ] Captura de `routes/api.php`.
-   [ ] Captura de `ProductoApiController.php`.
-   [ ] Captura de `CategoriaApiController.php`.

### Evidencia 3. Respuestas JSON

-   [ ] Captura del navegador en `http://127.0.0.1:8000/api/productos`.
-   [ ] Captura del navegador en `http://127.0.0.1:8000/api/categorias`.

### Evidencia 4. Consumo de la API

-   [ ] Captura del código JavaScript donde se observe `fetch()`.
-   [ ] Debe verse el procesamiento de JSON.
-   [ ] Debe verse la generación dinámica de tarjetas.

### Evidencia 5. Catálogo integrado

-   [ ] Captura del catálogo público.
-   [ ] Deben verse productos, fotografías, descripciones y precios.

### Evidencia 6. Filtros dinámicos

-   [ ] Captura de los botones de categorías.
-   [ ] Captura del resultado de seleccionar diferentes filtros.

### Evidencia 7. Prueba de integración

-   [ ] Captura de una categoría registrada desde el administrador.
-   [ ] Captura del producto asociado registrado desde el administrador.
-   [ ] Captura del catálogo después de actualizar, mostrando el
    producto y el filtro nuevos.

**Consejo para las capturas:** procura que se vea el nombre del archivo
o la dirección del navegador cuando corresponda. No uses capturas de
ejemplo: utiliza las de tu propio proyecto funcionando.

------------------------------------------------------------------------

## 14. Reflexión final

La actividad solicita responder estas ocho preguntas con base en lo que
desarrollaste:

1.  ¿Qué función desempeña una API REST dentro de una aplicación web?
2.  ¿Cuál es la responsabilidad del controlador API en Laravel?
3.  ¿Por qué se utiliza JSON para intercambiar información entre las
    aplicaciones?
4.  ¿Qué diferencia existe entre consultar información mediante una ruta
    web y hacerlo mediante un endpoint API?
5.  ¿Qué función desempeña `fetch()` en JavaScript?
6.  ¿Por qué es necesario configurar CORS cuando las aplicaciones
    utilizan diferentes orígenes?
7.  ¿Qué ventajas representa generar dinámicamente los productos y las
    categorías?
8.  ¿Qué modificaciones serían necesarias para que el catálogo se
    actualizara automáticamente sin que el cliente tuviera que recargar
    la página?

### Orientación para redactar las respuestas

Responde con tus propias palabras y relaciona cada explicación con la
práctica:

-   API REST: comunicación entre aplicaciones mediante endpoints.
-   Controlador API: consulta los modelos, organiza los datos y devuelve
    JSON.
-   JSON: formato estructurado para intercambiar información.
-   Ruta web y endpoint API: una ruta web suele atender páginas o
    vistas; el endpoint API entrega datos para que otra aplicación los
    procese.
-   `fetch()`: realiza solicitudes desde JavaScript.
-   CORS: controla qué orígenes pueden solicitar recursos.
-   Generación dinámica: evita escribir manualmente cada producto y cada
    filtro en HTML.
-   Actualización automática: la práctica no la implementa; para
    lograrla se necesitaría un mecanismo adicional de actualización
    periódica o comunicación en tiempo real. La actividad no especifica
    una tecnología concreta.

------------------------------------------------------------------------

# 15. Lista de verificación antes de entregar

## Laravel

-   [ ] `bootstrap/app.php` registra `routes/api.php`.
-   [ ] Existe `routes/api.php`.
-   [ ] Existe `ProductoApiController.php`.
-   [ ] Existe `CategoriaApiController.php`.
-   [ ] Las dos rutas aparecen en `php artisan route:list --path=api`.
-   [ ] Los dos endpoints devuelven JSON.
-   [ ] Las imágenes tienen direcciones accesibles.
-   [ ] CORS permite el origen real de la página estática.

## Página estática

-   [ ] `main.js` declara las dos direcciones API.
-   [ ] Existe `cargarProductos()`.
-   [ ] Existe `mostrarProductos()`.
-   [ ] Existe `cargarCategorias()`.
-   [ ] Existe `renderizarFiltros()`.
-   [ ] Existe `normalizarCategoria()`.
-   [ ] El evento de filtros está configurado.
-   [ ] `DOMContentLoaded` ejecuta la carga de categorías y productos.
-   [ ] `index.html` conserva `id="catalogo"` e `id="filtros"`.
-   [ ] `index.html` carga `js/main.js` con `defer`.

## Funcionamiento y entrega

-   [ ] Se muestran los productos reales de MariaDB.
-   [ ] Se muestran las fotografías.
-   [ ] Funcionan los filtros.
-   [ ] Una categoría nueva genera un filtro nuevo.
-   [ ] Un producto nuevo aparece al actualizar el catálogo.
-   [ ] Se reunieron las siete evidencias.
-   [ ] Se respondieron las ocho preguntas de reflexión.

------------------------------------------------------------------------

## 16. Problemas frecuentes y qué revisar

  -----------------------------------------------------------------------
  Problema                            Qué revisar
  ----------------------------------- -----------------------------------
  Error 404 en `/api/productos`       Que `api.php` exista, esté
                                      registrado en `bootstrap/app.php` y
                                      la ruta esté declarada.

  No aparecen productos               Que haya productos activos y
                                      categorías activas, y que la
                                      conexión a MariaDB funcione.

  Error de relación `categoria`       Que el modelo `Producto` tenga
                                      correctamente definida la relación
                                      `categoria`.

  Error de columna desconocida        Que los nombres de campos del
                                      controlador coincidan con las
                                      columnas y atributos reales del
                                      proyecto.

  Error CORS en consola               Que el origen y puerto de Live
                                      Server o Apache estén incluidos en
                                      `allowed_origins`, y que se haya
                                      ejecutado
                                      `php artisan config:clear`.

  No aparecen imágenes                Que se haya ejecutado
                                      `php artisan storage:link`, que el
                                      archivo exista y que su ruta sea
                                      correcta.

  No se muestran las tarjetas         Revisar la consola del navegador,
                                      el `id="catalogo"` y que `main.js`
                                      esté cargándose.

  Los filtros no hacen nada           Revisar `id="filtros"`,
                                      `data-filtro`, `dataset.categoria`
                                      y `normalizarCategoria()`.

  Los botones de WhatsApp o           Revisar que sus manejadores de
  cotización no funcionan             eventos también atiendan los
                                      elementos creados dinámicamente.
  -----------------------------------------------------------------------

### Nota de compatibilidad

Los fragmentos de código de esta guía reproducen la solución planteada
en la actividad. Antes de pegarlos, compara los nombres de modelos,
relaciones, columnas, rutas y campos de imagen con tu proyecto real. La
actividad presupone una estructura concreta y no proporciona el código
completo de los modelos ni del esquema de la base de datos.

------------------------------------------------------------------------

## 17. Resultado de aprendizaje esperado

Al finalizar, habrás implementado la comunicación entre dos aplicaciones
web independientes mediante una API REST, utilizando Laravel como
proveedor de información y JavaScript como consumidor de los servicios.

También habrás trabajado con:

-   Comunicación entre backend y frontend.
-   Consultas a una base de datos relacional.
-   Respuestas JSON.
-   Solicitudes HTTP con `fetch()`.
-   Configuración CORS.
-   Generación dinámica de tarjetas y filtros.

Esta integración constituye una base para desarrollar catálogos
digitales, sistemas empresariales, tiendas en línea y aplicaciones que
necesitan compartir información entre diferentes sistemas.
