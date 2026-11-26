<?php
$projectRoot = dirname(__DIR__, 2);
$applicationData = getenv('APPDATA') ?: getenv('USERPROFILE');
if (!$applicationData) {
    fwrite(STDERR, "No se pudo localizar el perfil de usuario para guardar la clave privada.\n");
    exit(1);
}

$privateDirectory = $applicationData . DIRECTORY_SEPARATOR . 'RepuestosApp';
$privateKeyPath = $privateDirectory . DIRECTORY_SEPARATOR . 'license-private.pem';
$publicDirectory = $projectRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Config';
$publicKeyPath = $publicDirectory . DIRECTORY_SEPARATOR . 'licencia_publica.pem';

if (file_exists($privateKeyPath) || file_exists($publicKeyPath)) {
    fwrite(STDERR, "Ya existe una clave de licencia. No se sobrescribio ninguna clave.\n");
    exit(1);
}

if (!is_dir($privateDirectory) && !mkdir($privateDirectory, 0700, true) && !is_dir($privateDirectory)) {
    fwrite(STDERR, "No se pudo crear la carpeta privada de claves.\n");
    exit(1);
}
if (!is_dir($publicDirectory) && !mkdir($publicDirectory, 0755, true) && !is_dir($publicDirectory)) {
    fwrite(STDERR, "No se pudo crear la carpeta de configuracion publica.\n");
    exit(1);
}

$configCandidates = [
    getenv('OPENSSL_CONF') ?: null,
    dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'openssl' . DIRECTORY_SEPARATOR . 'openssl.cnf',
    dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf',
];
$opensslConfig = null;
foreach ($configCandidates as $candidate) {
    if ($candidate && is_file($candidate)) {
        $opensslConfig = $candidate;
        break;
    }
}
if ($opensslConfig === null) {
    fwrite(STDERR, "No se encontro openssl.cnf. Define OPENSSL_CONF o configura PHP/OpenSSL.\n");
    exit(1);
}

putenv('OPENSSL_CONF=' . $opensslConfig);
putenv('HOME=' . $privateDirectory);
putenv('RANDFILE=' . $privateDirectory . DIRECTORY_SEPARATOR . '.rnd');
$key = openssl_pkey_new([
    'config' => $opensslConfig,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    'private_key_bits' => 3072,
]);
if ($key === false || !openssl_pkey_export($key, $privateKeyPem)) {
    fwrite(STDERR, "OpenSSL no pudo generar la clave privada.\n");
    while ($error = openssl_error_string()) {
        fwrite(STDERR, $error . "\n");
    }
    exit(1);
}

$keyDetails = openssl_pkey_get_details($key);
if (!$keyDetails || empty($keyDetails['key'])) {
    fwrite(STDERR, "OpenSSL no pudo obtener la clave publica.\n");
    exit(1);
}

if (file_put_contents($publicKeyPath, $keyDetails['key'], LOCK_EX) === false) {
    fwrite(STDERR, "No se pudo guardar la clave publica.\n");
    exit(1);
}
if (file_put_contents($privateKeyPath, $privateKeyPem, LOCK_EX) === false) {
    unlink($publicKeyPath);
    fwrite(STDERR, "No se pudo guardar la clave privada.\n");
    exit(1);
}

fwrite(STDOUT, "Claves creadas. La clave privada quedo fuera del proyecto en:\n{$privateKeyPath}\n");
fwrite(STDOUT, "Conserva una copia segura de esa clave; no la compartas ni la publiques.\n");