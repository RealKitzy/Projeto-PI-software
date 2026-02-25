<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Base

function getPDO(): PDO {
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $databaseUrl = getenv('DATABASE_URL');

    try {
        if ($databaseUrl) {

            $parts = parse_url($databaseUrl);

            if ($parts === false) {
                throw new RuntimeException('DATABASE_URL inválida.');
            }

            $host = $parts['host'] ?? 'localhost';
            $port = $parts['port'] ?? 5432;
            $user = $parts['user'] ?? '';
            $pass = $parts['pass'] ?? '';
            $db   = ltrim($parts['path'] ?? '/postgres', '/');

            $dsn = "pgsql:host={$host};port={$port};dbname={$db};";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } else {
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '5432';
            $db   = getenv('DB_NAME') ?: 'postgres';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: '';

            $dsn = "pgsql:host={$host};port={$port};dbname={$db};";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
    } catch (Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao conectar no banco de dados.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    return $pdo;
}