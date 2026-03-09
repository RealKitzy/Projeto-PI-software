<?php
declare(strict_types=1);

/**
 * PostgreSQL connection for Railway.
 *
 * Priority:
 * 1) DATABASE_URL (Railway/Heroku style)
 * 2) PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD
 */
function getPDO(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $databaseUrl = getenv('DATABASE_URL') ?: '';

  if ($databaseUrl) {
    $parts = parse_url($databaseUrl);
    if ($parts === false) {
      throw new RuntimeException('DATABASE_URL inválida.');
    }

    $host = $parts['host'] ?? 'localhost';
    $port = (int)($parts['port'] ?? 5432);
    $user = $parts['user'] ?? '';
    $pass = $parts['pass'] ?? '';
    $db   = ltrim($parts['path'] ?? '', '/');

    $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $db);
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
  }

  $host = getenv('PGHOST') ?: 'localhost';
  $port = (int)(getenv('PGPORT') ?: 5432);
  $db   = getenv('PGDATABASE') ?: '';
  $user = getenv('PGUSER') ?: '';
  $pass = getenv('PGPASSWORD') ?: '';

  if (!$db || !$user) {
    throw new RuntimeException('Credenciais do Postgres não encontradas no ambiente.');
  }

  $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $db);
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);

  return $pdo;
}
