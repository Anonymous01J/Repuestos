# Instalacion en Windows con XAMPP

## Requisitos

- Windows y XAMPP con Apache, PHP 8.0 o posterior y MariaDB 10.4 o posterior.
- Extensiones PHP `pdo_mysql` y `openssl` habilitadas.
- Acceso de administrador a MariaDB durante la importacion inicial.

El codigo requiere PHP 8.0 como minimo. Windows 7 solo permite usar paquetes antiguos de XAMPP/PHP, y PHP 8.0 ya no recibe actualizaciones de seguridad. Para una instalacion conectada a una red, se recomienda actualizar Windows y usar una version de PHP que siga recibiendo soporte. No expongas un servidor Windows 7 a Internet.

## Preparar los archivos

1. Copia el proyecto en `C:\xampp\htdocs\Repuestos`.
2. Incluye la carpeta `vendor/`: `index.php` carga `vendor/autoload.php`. No es necesario instalar Composer en el PC cliente. Si `vendor/` falta, genérala en el equipo de preparacion del paquete y copia la carpeta completa.
3. Confirma que exista `src/Config/licencia_publica.pem`. La clave privada no se copia al cliente.

## Habilitar extensiones PHP

1. En el panel de XAMPP, junto a Apache, abre **Config > PHP (php.ini)**. Edita el `php.ini` de `C:\xampp\php`, no otro `php.ini` instalado en Windows.
2. Busca estas directivas. Si tienen `;` al principio, quitalo; si no aparecen, agregalas en la seccion de extensiones:

```ini
extension=pdo_mysql
extension=openssl
```

3. Confirma que `extension_dir="ext"` apunte a `C:\xampp\php\ext` y que alli existan `php_pdo_mysql.dll` y `php_openssl.dll`.
4. Guarda `php.ini` y reinicia Apache desde el panel de XAMPP.
5. Abre **XAMPP Shell** y ejecuta:

```bat
C:\xampp\php\php.exe -m | findstr /I "pdo_mysql openssl"
```

La salida debe incluir `pdo_mysql` y `openssl`. Si no aparecen, revisa el `php.ini` que usa Apache desde **Apache > Config > PHP (php.ini)** y los registros de Apache. No dejes un archivo `phpinfo()` accesible desde la red.

## Preparar el dump de la base

El dump `src/Database/repuestos null.sql` crea la base `repuestos`, sus tablas, vistas, procedimiento de estadisticas, roles, tasa y usuarios. No importes tambien `repuestos ejemplo.sql`.

## Importar y configurar MariaDB

1. Inicia MySQL desde el panel de XAMPP.
2. Abre `http://localhost/phpmyadmin` e inicia sesion como administrador de MariaDB.
3. Importa `src/Database/repuestos null.sql`. El archivo ya contiene el procedimiento y las vistas de estadisticas; no hace falta importar `estadisticas_productos.sql` por separado.
4. Importa `src/Database/licencias.sql`. Es idempotente y asegura la tabla y la fila de licencia inicial.
5. Crea un usuario exclusivo para la aplicacion, en lugar de conectar PHP como `root`:

```sql
CREATE USER 'repuestos_app'@'localhost' IDENTIFIED BY 'REEMPLAZAR-POR-UNA-CLAVE-LARGA';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE
ON repuestos.* TO 'repuestos_app'@'localhost';
```

6. Edita `src/Models/conexion.php` y usa el mismo usuario y clave:

```php
$username = "repuestos_app";
$password = "REEMPLAZAR-POR-UNA-CLAVE-LARGA";
```

Guarda esos datos solo en el servidor cliente. No reutilices la contrasena de MariaDB en otras cuentas.

## Activar la licencia

1. En el equipo emisor, genera un token para el cliente con un identificador unico:

```powershell
php tools/licencias/emitir_token.php CLIENTE-EMPRESA-001
```

2. Entrega el token generado. Su vigencia empieza al emitirlo y dura 45 dias.
3. Abre `http://localhost/Repuestos/`, pega el token en la pantalla de licencia y activa el sistema.
4. La aplicacion guarda el estado anti-retroceso en `%APPDATA%\RepuestosApp\license-state.json`. Ejecuta Apache bajo la cuenta de Windows que usara el sistema y asegurate de que esa cuenta pueda escribir en su perfil.
5. Mantén Windows sincronizado con hora de red. Para una recuperacion completa, conserva una copia reciente de `license-state.json` junto con el backup de la base. No restaures un archivo de estado antiguo junto con una base restaurada sin revisar que el token y las fechas correspondan.

La clave privada de firma se conserva solo en el equipo emisor, en `%APPDATA%\RepuestosApp\license-private.pem`. No la copies al cliente ni la incluyas en el proyecto distribuido.

## Prueba final

- Inicia Apache y MySQL desde XAMPP.
- Abre `http://localhost/Repuestos/`.
- Activa la licencia, inicia sesion con la cuenta administrativa preparada y comprueba una entrada y una venta de prueba para confirmar que el stock cambia una sola vez.
- Comprueba que el dashboard y las estadisticas carguen correctamente.

## Copias de seguridad

Ejecuta `backup_db.bat` para crear un dump en `backups/`. Guarda tambien una copia reciente de `%APPDATA%\RepuestosApp\license-state.json` en un respaldo protegido separado. No distribuyas copias de la clave privada de firma junto con los backups del cliente.
