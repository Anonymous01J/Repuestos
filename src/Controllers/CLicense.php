<?php
namespace Anonymous01j\Repuestos\Controllers;

use Anonymous01j\Repuestos\Services\LicenseService;

class CLicense
{
    public function settings(): void
    {
        if (!isset($_SESSION['id'])) {
            header('Location: ?c=CHome/view');
            exit;
        }

        try {
            $licenseSummary = (new LicenseService())->getSummary();
        } catch (\Throwable $exception) {
            $licenseSummary = ['status' => 'storage_unavailable'];
        }

        $licenseFeedback = $_SESSION['license_feedback'] ?? null;
        unset($_SESSION['license_feedback']);
        $_SESSION['license_csrf'] = bin2hex(random_bytes(32));
        $title = 'Configuración de licencia';
        require __DIR__ . '/../Views/configuracion_licencia.php';
    }

    public function view(): void
    {
        try {
            $licenseStatus = (new LicenseService())->check();
        } catch (\Throwable $exception) {
            $licenseStatus = 'storage_unavailable';
        }

        $licenseMessages = [
            'active' => 'La licencia está activa.',
            'activation_required' => 'Activa el sistema con un token de licencia proporcionado por el proveedor.',
            'expired' => 'La licencia venció. Ingresa un token vigente para continuar. Comunicarse con el proveedor si no se tiene una vigente.',
            'invalid_token' => 'El token guardado no es válido. Ingresa un token vigente.',
            'clock_rollback' => 'La fecha del servidor es anterior a la última fecha registrada. Sincroniza el reloj de Windows para continuar.',
            'external_state_missing' => 'Falta el registro de licencia fuera de la base de datos. Pega nuevamente el token vigente para inicializarlo.',
            'external_state_invalid' => 'El registro externo de licencia está dañado. Pega nuevamente un token vigente para reconstruirlo.',
            'external_state_mismatch' => 'La licencia no coincide con el registro externo. Puede haberse restaurado una copia anterior; reactiva el token vigente.',
            'external_state_unavailable' => 'No se pudo guardar el registro externo. Verifica los permisos de escritura del perfil de Windows.',
            'signing_key_missing' => 'Falta configurar la clave pública de licencias en src/Config/licencia_publica.pem.',
            'storage_unavailable' => 'No se pudo leer la licencia. Verifica MySQL, la migración src/Database/licencias.sql y el acceso a la carpeta de estado.',
        ];
        $licenseMessage = $licenseMessages[$licenseStatus] ?? $licenseMessages['storage_unavailable'];
        $licenseFeedback = $_SESSION['license_feedback'] ?? null;
        unset($_SESSION['license_feedback']);
        $_SESSION['license_csrf'] = bin2hex(random_bytes(32));

        require_once __DIR__ . '/../Views/licencia.php';
    }

    public function activate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?c=CLicense/view');
            exit;
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!is_string($csrfToken) || !hash_equals($_SESSION['license_csrf'] ?? '', $csrfToken)) {
            $_SESSION['license_feedback'] = 'La solicitud expiró. Vuelve a cargar la página e inténtalo de nuevo.';
            header('Location: ?c=CLicense/view');
            exit;
        }

        try {
            $result = (new LicenseService())->activate((string) ($_POST['token'] ?? ''));
        } catch (\Throwable $exception) {
            $result = 'storage_unavailable';
        }

        if ($result === 'active') {
            header('Location: ?c=CHome/view');
            exit;
        }

        $feedback = [
            'invalid_token' => 'El token no es válido o fue alterado.',
            'expired' => 'El token ya venció. Solicita uno nuevo.',
            'clock_rollback' => 'La fecha del servidor es anterior a la emisión del token. Sincroniza el reloj de Windows y vuelve a intentarlo.',
            'external_state_mismatch' => 'Ese token es anterior o pertenece a otra licencia. Ingresa el token vigente asociado a este servidor.',
            'external_state_unavailable' => 'No se pudo guardar el registro externo. Verifica los permisos de escritura del perfil de Windows.',
            'signing_key_missing' => 'No está configurada la clave pública de licencias.',
            'storage_unavailable' => 'No se pudo guardar la licencia. Verifica MySQL y la migración de la tabla.',
        ];
        $_SESSION['license_feedback'] = $feedback[$result] ?? 'No se pudo activar la licencia.';
        header('Location: ?c=CLicense/view');
        exit;
    }
}