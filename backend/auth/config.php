<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

// ==== Secure session defaults ====
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

$IS_HTTPS = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_secure', $IS_HTTPS ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');

session_name('SID');
session_start();

function json_response(array $payload, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function require_method(string $method): void {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== $method) {
    json_response(['success' => false, 'message' => 'Método não permitido.'], 405);
  }
}

function read_input(): array {
  $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($contentType, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    return is_array($data) ? $data : [];
  }
  return $_POST ?? [];
}

function normalize_email(string $email): string {
  return trim(mb_strtolower($email));
}

function is_valid_email(string $email): bool {
  return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Mapeamento do front:
 * - "gerador" => tipo_usuario "empresa"
 * - "catador_cooperativa" => tipo_usuario "catador"
 *
 * Também aceita receber direto "empresa" / "catador".
 */
function map_tipo_usuario(string $perfil): ?string {
  $perfil = trim(mb_strtolower($perfil));
  if ($perfil === 'empresa' || $perfil === 'catador') return $perfil;
  if ($perfil === 'gerador') return 'empresa';
  if ($perfil === 'catador_cooperativa') return 'catador';
  return null;
}

function set_login_session(array $user): void {
  session_regenerate_id(true);
  $_SESSION['user'] = [
    'id' => (int)$user['id'],
    'email' => (string)$user['email'],
    'tipo_usuario' => (string)$user['tipo_usuario'],
  ];
}

function current_user(): ?array {
  return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}
