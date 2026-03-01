<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

$IS_HTTPS = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_secure', $IS_HTTPS ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');

session_name('SID');
session_start();

define('STORAGE_DIR', __DIR__ . '/../storage');
define('USERS_FILE', STORAGE_DIR . '/users.json');
define('RL_FILE', STORAGE_DIR . '/ratelimit.json');

if (!is_dir(STORAGE_DIR)) {
  @mkdir(STORAGE_DIR, 0755, true);
}

function json_response(array $payload, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
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

function is_valid_role(string $role): bool {
  return in_array($role, ['gerador', 'catador_cooperativa'], true);
}

function safe_name(string $name): string {
  $name = trim($name);
  $name = preg_replace('/\s+/', ' ', strip_tags($name)) ?? '';
  return mb_substr($name, 0, 80);
}

function read_json_file(string $path, array $default): array {
  if (!file_exists($path)) return $default;
  $raw = file_get_contents($path);
  $data = json_decode($raw ?: '', true);
  return is_array($data) ? $data : $default;
}

function write_json_file_atomic(string $path, array $data): void {
  $tmp = $path . '.tmp';
  file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
  rename($tmp, $path);
}

function load_users(): array {
  return read_json_file(USERS_FILE, []);
}

function save_users(array $users): void {
  write_json_file_atomic(USERS_FILE, $users);
}

function find_user_by_email(string $email): ?array {
  $users = load_users();
  foreach ($users as $u) {
    if (($u['email'] ?? '') === $email) return $u;
  }
  return null;
}

function rate_limit(string $key, int $maxAttempts, int $windowSeconds): void {
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $now = time();

  $db = read_json_file(RL_FILE, []);
  if (!isset($db[$key])) $db[$key] = [];
  if (!isset($db[$key][$ip])) $db[$key][$ip] = [];

  $events = array_filter($db[$key][$ip], function($t) use ($now, $windowSeconds) {
    return is_int($t) && ($now - $t) < $windowSeconds;
  });
  $events = array_values($events);

  if (count($events) >= $maxAttempts) {
    json_response(['success' => false, 'message' => 'Muitas tentativas. Aguarde um pouco e tente novamente.'], 429);
  }

  $events[] = $now;
  $db[$key][$ip] = $events;
  write_json_file_atomic(RL_FILE, $db);
}

function require_method(string $method): void {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== $method) {
    json_response(['success' => false, 'message' => 'Método não permitido.'], 405);
  }
}

function set_login_session(array $user): void {
  session_regenerate_id(true);
  $_SESSION['user'] = [
    'id' => $user['id'],
    'nome' => $user['nome'],
    'email' => $user['email'],
    'perfil' => $user['perfil'],
  ];
}

function current_user(): ?array {
  return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}
?>