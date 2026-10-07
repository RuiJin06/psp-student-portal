<?php
declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $c = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$c['host']};dbname={$c['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $c['username'], $c['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); exit('Invalid request token. Please try again.');
    }
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user()) { header('Location: ?page=login'); exit; } }
function flash(string $key, ?string $value = null): ?string {
    if ($value !== null) { $_SESSION['flash'][$key] = $value; return null; }
    $value = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $value;
}
