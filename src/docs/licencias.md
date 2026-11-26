# Licencias

El sistema acepta tokens RSA firmados que vencen exactamente 45 dias despues de su emision. La clave privada se conserva solo en el equipo emisor; la aplicacion usa la clave publica.

## Primera configuracion

1. Inicia MySQL desde XAMPP.
2. En una base ya existente, importa `src/Database/licencias.sql` desde phpMyAdmin. Las bases nuevas creadas desde los esquemas del proyecto ya incluyen la tabla.
3. En el equipo emisor, ejecuta `php tools/licencias/generar_claves.php` una sola vez.
4. El script guarda la clave privada en `%APPDATA%\RepuestosApp\license-private.pem` y la clave publica en `src/Config/licencia_publica.pem`.
5. Conserva una copia segura de la clave privada. No la publiques ni la copies al equipo cliente. Perderla impide emitir tokens que la aplicacion acepte.

## Emitir y activar

En el equipo emisor, ejecuta `php tools/licencias/emitir_token.php CLIENTE-001`, sustituyendo el identificador por uno propio. Copia el token generado y pégalo en la pantalla de licencia del sistema. Cada token comienza su vigencia al emitirse y expira a los 45 dias.

Con la licencia activa, el dashboard muestra su estado, fecha de vencimiento y dias restantes. A partir de 20 dias aparece una advertencia para contactar al proveedor; con 10 dias o menos, se indica que el sistema entrara en suspension al vencer si no se coordina la renovacion. Para renovar, abre **Configuracion de licencia** desde el dashboard, solicita al proveedor un token nuevo usando el mismo identificador de licencia y pegalo en el formulario. El nuevo periodo de 45 dias empieza cuando se emite el token.

## Reloj del servidor

La aplicacion guarda la ultima fecha observada, el identificador de licencia y el identificador del token tanto en la base de datos como en `%APPDATA%\RepuestosApp\license-state.json`. Si Windows arranca con una fecha anterior, el sistema pide sincronizar el reloj antes de continuar. El archivo no forma parte de los backups SQL: al restaurar solo la base de datos, la aplicacion detecta una fecha anterior o un token distinto y pide reactivar con el token vigente. Mantén activo el servicio de hora de Windows y no incluyas el archivo externo en restauraciones de base de datos.

Si el archivo falta o está dañado, vuelve a activar con un token vigente para reconstruirlo. Conserva también una copia reciente del archivo si necesitas recuperar el servidor completo.

El control sigue siendo local: una restauracion completa del perfil de Windows junto con la base de datos puede restaurar ambos registros. Para resistir esa situacion se necesita un servidor de licencias externo.