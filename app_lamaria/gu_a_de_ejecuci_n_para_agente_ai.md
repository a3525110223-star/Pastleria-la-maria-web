# Guía de Ejecución Automatizada: Integración Módulo Empresa (Laravel + API REST + Frontend)

**Objetivo del Agente:** Exponer la información almacenada en MariaDB (módulo Empresa) a través de una API REST en Laravel y consumirla dinámicamente desde el sitio web público de Pastelería La María usando JavaScript (`fetch()`), manteniendo la separación entre backend y frontend.

---

## 1. Tareas de Backend (Laravel)

### Tarea 1.1: Verificar el Modelo de Empresa
Asegúrate de que exista el modelo `Empresa` en `app/Models/Empresa.php` asociado a la tabla de la base de datos (por ejemplo, `empresas`). Si no existe, créalo o adapta el nombre del modelo existente.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas'; // Asegurar el nombre correcto de la tabla

    protected $fillable = [
        'nombre',
        'mision',
        'vision',
        'telefono',
        'direccion',
        'email'
    ];
}
```

---

### Tarea 1.2: Crear el Controlador API
Crea o edita el controlador en `app/Http/Controllers/Api/EmpresaController.php`. Debe retornar los datos de la empresa en formato JSON con la respuesta HTTP adecuada.

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\JsonResponse;

class EmpresaController extends Controller
{
    /**
     * Obtener los datos generales de la empresa.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $empresa = Empresa::first();

        if (!$empresa) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se encontraron datos registrados de la empresa.'
            ], 404);
        }

        return response()->json($empresa, 200);
    }
}
```

---

### Tarea 1.3: Registrar la Ruta API
En el archivo `routes/api.php`, define la ruta GET para el endpoint `/empresa`:

```php
use App\Http\Controllers\Api\EmpresaController;
use Illuminate\Support\Facades\Route;

Route::get('/empresa', [EmpresaController::class, 'show']);
```

---

### Tarea 1.4: Configurar Middleware CORS (Si aplica)
Asegúrate de que en `config/cors.php` la ruta `api/*` tenga habilitado el acceso desde orígenes externos para evitar bloqueos del navegador al realizar peticiones con `fetch()`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

---

## 2. Tareas de Frontend (HTML + JavaScript)

### Tarea 2.1: Asignar IDs en el HTML Público
Ubica la sección "Nosotros" o "Empresa" en el archivo HTML del sitio público (`index.html` o la vista correspondiente) y asigna identificadores (`id`) a los elementos donde se insertará la información:

```html
<!-- Sección Empresa en la página pública -->
<section id="seccion-empresa">
    <h1 id="empresa-nombre">Cargando...</h1>
    
    <div class="mision-container">
        <h3>Misión</h3>
        <p id="empresa-mision">Cargando información...</p>
    </div>

    <div class="vision-container">
        <h3>Visión</h3>
        <p id="empresa-vision">Cargando información...</p>
    </div>

    <div class="contacto-info">
        <p><strong>Teléfono:</strong> <span id="empresa-telefono">---</span></p>
        <p><strong>Dirección:</strong> <span id="empresa-direccion">---</span></p>
    </div>
</section>
```

---

### Tarea 2.2: Implementar la petición fetch() en JS
Crea o edita un script JS (ej. `public/js/empresa.js` o dentro del tag `<script>` al final de la página web pública) con el siguiente contenido:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    // Definir la URL de la API en Laravel
    const API_URL = 'http://127.0.0.1:8000/api/empresa';

    fetchEmpresaInfo(API_URL);
});

function fetchEmpresaInfo(url) {
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            renderEmpresaData(data);
        })
        .catch(error => {
            console.error('Error al consumir la API de Empresa:', error);
        });
}

function renderEmpresaData(data) {
    const elNombre = document.getElementById('empresa-nombre');
    const elMision = document.getElementById('empresa-mision');
    const elVision = document.getElementById('empresa-vision');
    const elTelefono = document.getElementById('empresa-telefono');
    const elDireccion = document.getElementById('empresa-direccion');

    if (elNombre && data.nombre) elNombre.textContent = data.nombre;
    if (elMision && data.mision) elMision.textContent = data.mision;
    if (elVision && data.vision) elVision.textContent = data.vision;
    if (elTelefono && data.telefono) elTelefono.textContent = data.telefono;
    if (elDireccion && data.direccion) elDireccion.textContent = data.direccion;
}
```

---

## 3. Checklist de Evidencias para Entregables (Usuario)

Revisa que tu agente haya completado todo de forma correcta. Toma las siguientes capturas para cubrir los criterios de evaluación:

1. **Ruta y Controlador (20%):**
   - [ ] Captura del archivo `routes/api.php` donde se visualice `Route::get('/empresa', ...)`.
   - [ ] Captura del archivo `app/Http/Controllers/Api/EmpresaController.php`.

2. **Respuesta JSON de la API (15%):**
   - [ ] Captura del navegador web o Postman consultando `http://127.0.0.1:8000/api/empresa` mostrando los datos en formato JSON.

3. **Consumo con Fetch y JavaScript (20%):**
   - [ ] Captura del archivo JavaScript o bloque `<script>` mostrando la función `fetch()` hacia el endpoint de la API.

4. **Integración Dinámica en HTML (15%):**
   - [ ] Captura del sitio web público mostrando los datos de la empresa cargados en pantalla respetando el diseño gráfico.

5. **Prueba de actualización dinámica (15%):**
   - [ ] Captura del panel de administración editando un campo (ej. modificando el teléfono o la misión).
   - [ ] Captura de la página pública recién actualizada (F5) mostrando el nuevo valor recibido desde la base de datos.

6. **Explicación del Proceso (15%):**
   - [ ] Redacción de una breve síntesis en tu reporte explicando cómo se conecta la base de datos MariaDB -> Laravel -> API REST -> JS Fetch -> HTML DOM.