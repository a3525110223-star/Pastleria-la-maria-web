# Implementación de Usuarios, Roles y Empresa

## Alcance y estructura

El esquema aplicable está en `database/migrations` y su referencia MariaDB/MySQL está en `database/schema.sql`. Se mantiene la tabla `users` del esqueleto Laravel; las migraciones nuevas crean `rol`, `rol_usuario` y `empresa`. La tabla pivote tiene clave propia, timestamps, unicidad usuario/rol y borrado en cascada. El archivo SQL es una alternativa de creación manual para una base vacía: no ejecutes el SQL y las migraciones sobre la misma base. Si las tablas ya se crearon manualmente, respalda la base y compara el esquema/estado de migraciones antes de aplicar cambios; no ejecutes una migración `create` a ciegas sobre tablas existentes.

Los roles Administrador, Empleado y Cliente y el usuario inicial `admin@lamaria.com` se inicializan mediante `RoleSeeder`, invocado por `DatabaseSeeder`. Antes de sembrar una base nueva, define una contraseña no compartida de al menos 12 caracteres en `SEED_ADMIN_PASSWORD`. Por ejemplo, establece esa variable en el entorno de despliegue (no la guardes en el repositorio) y ejecuta:

```powershell
php artisan migrate --force
php artisan db:seed --force
```

El seeder no cambia la contraseña de un administrador existente; si el usuario ya existe, solo asegura que tenga el rol Administrador. El SQL inserta los tres roles, mientras que la creación de la cuenta inicial se deja al seeder para que la contraseña se almacene con `Hash::make()`.

## Autorización y rutas

- Las rutas requieren `auth`; las rutas de administración también requieren el alias `admin`.
- Invitados son redirigidos a `/login`; Empleados reciben HTTP 403 en Usuarios, Roles y Empresa.
- `/usuarios` conserva los nombres Laravel `users.index`, `users.create`, `users.store`, `users.edit`, `users.update` y `users.destroy`.
- `/users` es un alias adicional y usa nombres `users.english.*`.
- Productos y Categorías continúan usando sus controladores/vistas existentes y se exponen bajo el grupo autenticado.
- El menú de Usuarios, Roles y Empresa solo se renderiza para usuarios con `isAdmin()`.
- Los roles base no se pueden renombrar ni eliminar. Los roles asignados a usuarios tampoco se pueden eliminar.
- No se permite eliminar ni cambiar a Empleado al último administrador.

## Verificación automatizada

Ejecuta `php artisan test`. Matriz de los casos cubiertos por `tests/Feature/AdminAccessTest.php`:

| ID | Escenario/Rol | Ruta/Acción | Resultado Esperado | Resultado Obtenido | Estatus |
|---|---|---|---|---|---|
| F7-01 | Cualquier usuario | Generación de rutas de Usuarios | Existen `/usuarios` y `/users` con nombres internos estables | Verificado por prueba automatizada | Pasó |
| F7-02 | Invitado | GET `/usuarios` y `/empresa` | Redirección a `/login` | Verificado por prueba automatizada | Pasó |
| F7-03 | Invitado | POST `/login` y `/logout` | Inicia sesión y la cierra invalidando la sesión | Verificado por prueba automatizada | Pasó |
| F7-04 | Administrador | GET Usuarios y Empresa | HTTP 200; menú administrativo visible | Verificado por prueba automatizada | Pasó |
| F7-05 | Empleado | GET Usuarios, `/users`, Roles y Empresa | HTTP 403; opciones administrativas ocultas | Verificado por prueba automatizada | Pasó |
| F7-06 | Administrador | POST `/usuarios` con rol Empleado y Cliente | Crea Empleado, almacena hash; rechaza Cliente | Verificado por prueba automatizada | Pasó |
| F7-07 | Último Administrador | DELETE y cambio a Empleado | Operaciones bloqueadas y conserva el último Administrador | Verificado por prueba automatizada | Pasó |
| F7-08 | Administrador | PUT `/empresa` | Persiste los datos del registro central con `id = 1` | Verificado por prueba automatizada | Pasó |
| F7-09 | Administrador | GET `/roles` y DELETE del rol base | Lista roles y no elimina Administrador | Verificado por prueba automatizada | Pasó |
| F7-10 | Instalación nueva | Ejecutar `RoleSeeder` | Crea roles y administrador con password hash configurable | Verificado por prueba automatizada | Pasó |
| F7-11 | Administrador | Crear, editar y eliminar rol personalizado | Persiste los cambios del CRUD | Verificado por prueba automatizada | Pasó |

## Compatibilidad

Este código usa la configuración de middleware y rutas compatible con Laravel 11. El `composer.json` de este proyecto actualmente declara `laravel/framework` `^12.0`; revisa esa discrepancia antes de desplegar si el destino debe ser estrictamente Laravel 11.
