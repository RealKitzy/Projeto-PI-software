<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('POST');
rate_limit('login', 12, 600);

$in = read_input();

$email = normalize_email((string)($in['email'] ?? ''));
$senha = (string)($in['senha'] ?? '');

if ($email === '' || $senha === '') {
  json_response(['success' => false, 'message' => 'E-mail e senha são obrigatórios.'], 400);
}
if (!is_valid_email($email)) {
  json_response(['success' => false, 'message' => 'E-mail inválido.'], 400);
}

$user = find_user_by_email($email);

if (!$user || !isset($user['password_hash']) || !password_verify($senha, (string)$user['password_hash'])) {
  json_response(['success' => false, 'message' => 'Credenciais inválidas.'], 401);
}

set_login_session($user);

json_response(['success' => true, 'message' => 'Login realizado com sucesso.', 'user' => current_user()]);
?>