<?php
namespace Anonymous01j\Repuestos\Services;

use Anonymous01j\Repuestos\Models\Conexion;
use PDO;

class LicenseService
{
    private PDO $connection;
    private string $publicKeyPath;
    private string $stateFilePath;

    public function __construct(?PDO $connection = null, ?string $stateFilePath = null)
    {
        $this->connection = $connection ?? Conexion::getInstance();
        $this->publicKeyPath = dirname(__DIR__) . '/Config/licencia_publica.pem';
        $stateDirectory = getenv('APPDATA') ?: getenv('USERPROFILE') ?: sys_get_temp_dir();
        $this->stateFilePath = $stateFilePath
            ?? rtrim($stateDirectory, '\\/') . DIRECTORY_SEPARATOR . 'RepuestosApp' . DIRECTORY_SEPARATOR . 'license-state.json';
    }

    public function check(): string
    {
        if (!is_file($this->publicKeyPath)) {
            return 'signing_key_missing';
        }

        $statement = $this->connection->query('SELECT token, ultima_fecha_vista_utc FROM licencia_sistema WHERE id = 1');
        $license = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$license || empty($license['token'])) {
            return 'activation_required';
        }

        $claims = $this->decodeAndVerifyToken($license['token']);
        if ($claims === null) {
            return 'invalid_token';
        }

        $externalState = $this->readExternalState();
        if ($externalState === null) {
            return 'external_state_missing';
        }
        if (empty($externalState['valid'])) {
            return 'external_state_invalid';
        }
        if (
            $externalState['license_id'] !== $claims['license_id']
            || $externalState['token_id'] !== $claims['token_id']
            || $externalState['issued_at'] !== $claims['issued_at']
        ) {
            return 'external_state_mismatch';
        }

        $now = time();
        $databaseLastSeen = $license['ultima_fecha_vista_utc'] === null
            ? 0
            : (int) $license['ultima_fecha_vista_utc'];
        $lastSeen = max($databaseLastSeen, $externalState['last_seen_utc']);
        if ($now < $claims['issued_at'] || $now < $lastSeen) {
            return 'clock_rollback';
        }

        if ($now > $lastSeen) {
            if (!$this->writeExternalState($claims, $now)) {
                return 'external_state_unavailable';
            }
            $update = $this->connection->prepare(
                'UPDATE licencia_sistema SET ultima_fecha_vista_utc = '
                . 'CASE WHEN ultima_fecha_vista_utc IS NULL OR ultima_fecha_vista_utc < :current_time '
                . 'THEN :new_time ELSE ultima_fecha_vista_utc END WHERE id = 1'
            );
            $update->execute(['current_time' => $now, 'new_time' => $now]);
        }

        if ($now >= $claims['expires_at']) {
            return 'expired';
        }

        return 'active';
    }

    public function getSummary(): array
    {
        $status = $this->check();
        $summary = [
            'status' => $status,
            'expires_at' => null,
            'days_remaining' => null,
            'alert_level' => null,
            'alert_message' => null,
        ];

        if (!in_array($status, ['active', 'expired'], true)) {
            return $summary;
        }

        $statement = $this->connection->query('SELECT token FROM licencia_sistema WHERE id = 1');
        $license = $statement->fetch(PDO::FETCH_ASSOC);
        $claims = $license ? $this->decodeAndVerifyToken($license['token']) : null;
        if ($claims === null) {
            $summary['status'] = 'invalid_token';
            return $summary;
        }

        $secondsRemaining = $claims['expires_at'] - time();
        $daysRemaining = max(0, (int) ceil($secondsRemaining / 86400));
        $summary['expires_at'] = $claims['expires_at'];
        $summary['days_remaining'] = $daysRemaining;

        if ($secondsRemaining <= 0) {
            $summary['alert_level'] = 'critical';
            $summary['alert_message'] = 'La licencia venció. Contacta al proveedor para renovarla y reactivar el sistema.';
        } elseif ($daysRemaining <= 10) {
            $summary['alert_level'] = 'critical';
            $summary['alert_message'] = "La licencia vence en {$daysRemaining} días. Si no te pones en contacto con el proveedor, el sistema entrará en suspensión al vencer.";
        } elseif ($daysRemaining <= 20) {
            $summary['alert_level'] = 'warning';
            $summary['alert_message'] = "La licencia vence en {$daysRemaining} días. Contacta al proveedor para renovarla.";
        }

        return $summary;
    }

    public function activate(string $token): string
    {
        if (!is_file($this->publicKeyPath)) {
            return 'signing_key_missing';
        }

        $claims = $this->decodeAndVerifyToken(trim($token));
        if ($claims === null) {
            return 'invalid_token';
        }

        $now = time();
        if ($now < $claims['issued_at']) {
            return 'clock_rollback';
        }
        $lastSeenQuery = $this->connection->query(
            'SELECT ultima_fecha_vista_utc FROM licencia_sistema WHERE id = 1'
        );
        $databaseLastSeen = $lastSeenQuery->fetchColumn();
        $externalState = $this->readExternalState();
        if ($externalState !== null && empty($externalState['valid'])) {
            $externalState = null;
        }
        $lastSeen = $databaseLastSeen === false || $databaseLastSeen === null
            ? 0
            : (int) $databaseLastSeen;
        if ($externalState !== null) {
            if (
                $externalState['license_id'] !== $claims['license_id']
                || $claims['issued_at'] < $externalState['issued_at']
                || (
                    $claims['issued_at'] === $externalState['issued_at']
                    && $claims['token_id'] !== $externalState['token_id']
                )
            ) {
                return 'external_state_mismatch';
            }
            $lastSeen = max($lastSeen, $externalState['last_seen_utc']);
        }
        if ($now < $lastSeen) {
            return 'clock_rollback';
        }
        if ($now >= $claims['expires_at']) {
            return 'expired';
        }

        if (!$this->writeExternalState($claims, max($now, $lastSeen))) {
            return 'external_state_unavailable';
        }

        $existing = $this->connection->query('SELECT id FROM licencia_sistema WHERE id = 1')->fetchColumn();
        if ($existing === false) {
            $insert = $this->connection->prepare(
                'INSERT INTO licencia_sistema (id, token, ultima_fecha_vista_utc) VALUES (1, :token, :last_seen)'
            );
            $insert->execute(['token' => trim($token), 'last_seen' => $now]);
        } else {
            $update = $this->connection->prepare(
                'UPDATE licencia_sistema SET token = :token, ultima_fecha_vista_utc = :last_seen WHERE id = 1'
            );
            $update->execute(['token' => trim($token), 'last_seen' => $now]);
        }
        return 'active';
    }

    private function readExternalState(): ?array
    {
        if (!is_file($this->stateFilePath)) {
            return null;
        }

        $contents = file_get_contents($this->stateFilePath);
        if ($contents === false) {
            return ['valid' => false];
        }

        try {
            $state = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return ['valid' => false];
        }

        if (
            !is_array($state)
            || !isset($state['license_id'], $state['token_id'], $state['issued_at'], $state['last_seen_utc'])
            || !is_string($state['license_id'])
            || !is_string($state['token_id'])
            || !is_int($state['issued_at'])
            || !is_int($state['last_seen_utc'])
            || $state['last_seen_utc'] <= 0
        ) {
            return ['valid' => false];
        }

        $state['valid'] = true;
        return $state;
    }

    private function writeExternalState(array $claims, int $lastSeen): bool
    {
        $directory = dirname($this->stateFilePath);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            return false;
        }

        try {
            $contents = json_encode([
                'license_id' => $claims['license_id'],
                'token_id' => $claims['token_id'],
                'issued_at' => $claims['issued_at'],
                'last_seen_utc' => $lastSeen,
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return false;
        }

        return file_put_contents($this->stateFilePath, $contents, LOCK_EX) === strlen($contents);
    }

    private function decodeAndVerifyToken(string $token): ?array
    {
        if (!is_file($this->publicKeyPath)) {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        $payloadJson = $this->base64UrlDecode($parts[0]);
        $signature = $this->base64UrlDecode($parts[1]);
        $publicKeyPem = file_get_contents($this->publicKeyPath);
        $publicKey = $publicKeyPem === false ? false : openssl_pkey_get_public($publicKeyPem);
        if ($payloadJson === null || $signature === null || $publicKey === false) {
            return null;
        }

        if (openssl_verify($payloadJson, $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
            return null;
        }

        try {
            $claims = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            return null;
        }

        if (
            !is_array($claims)
            || ($claims['version'] ?? null) !== 1
            || !isset($claims['license_id'], $claims['issued_at'], $claims['expires_at'], $claims['token_id'])
            || !is_string($claims['license_id'])
            || !preg_match('/^[A-Za-z0-9._-]{1,100}$/', $claims['license_id'])
            || !is_int($claims['issued_at'])
            || $claims['issued_at'] <= 0
            || !is_int($claims['expires_at'])
            || !is_string($claims['token_id'])
            || !preg_match('/^[a-f0-9]{32}$/', $claims['token_id'])
            || $claims['expires_at'] !== $claims['issued_at'] + (45 * 86400)
        ) {
            return null;
        }

        return $claims;
    }

    private function base64UrlDecode(string $value): ?string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/') . str_repeat('=', (4 - strlen($value) % 4) % 4), true);
        return $decoded === false ? null : $decoded;
    }
}