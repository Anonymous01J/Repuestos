<?php
if (PHP_SAPI !== 'cli' || $argc !== 2 || !preg_match('/^[A-Za-z0-9._-]{1,100}$/', $argv[1])) {
    fwrite(STDERR, "Uso: php tools/licencias/emitir_token.php <id-licencia>\n");
    exit(1);
}

$applicationData = getenv('APPDATA') ?: getenv('USERPROFILE');
if (!$applicationData) {
    fwrite(STDERR, "No se pudo localizar el perfil de usuario de Windows.\n");
    exit(1);
}
$privateKeyPath = $applicationData . DIRECTORY_SEPARATOR . 'RepuestosApp' . DIRECTORY_SEPARATOR . 'license-private.pem';
$privateKeyPem = is_file($privateKeyPath) ? file_get_contents($privateKeyPath) : false;
$privateKey = $privateKeyPem === false ? false : openssl_pkey_get_private($privateKeyPem);
if ($privateKey === false) {
    fwrite(STDERR, "No se pudo abrir la clave privada. Ejecuta primero generar_claves.php en el equipo emisor.\n");
    exit(1);
}

$issuedAt = time();
$claims = [
    'version' => 1,
    'license_id' => $argv[1],
    'issued_at' => $issuedAt,
    'expires_at' => $issuedAt + (45 * 86400),
    'token_id' => bin2hex(random_bytes(16)),
];
$payload = json_encode($claims, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
if (!openssl_sign($payload, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
    fwrite(STDERR, "OpenSSL no pudo firmar el token.\n");
    exit(1);
}

$base64UrlEncode = static fn(string $value): string => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
$token = $base64UrlEncode($payload) . '.' . $base64UrlEncode($signature);
fwrite(STDOUT, "Licencia: {$claims['license_id']}\n");
fwrite(STDOUT, 'Vence (UTC): ' . gmdate('Y-m-d H:i:s', $claims['expires_at']) . "\n");
fwrite(STDOUT, "Token:\n{$token}\n");