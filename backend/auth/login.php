<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('POST');
<<<<<<< HEAD
=======
rate_limit('login', 12, 600);

>>>>>>> 07292da532e8a6136c47337d1511d7d2976f4e5c
$in = read_input();

$email = normalize_email((string)($in['email'] ?? ''));
$senha = (string)($in['senha'] ?? '');

if ($email === '' || $senha === '') {
  json_response(['success' => false, 'message' => 'E-mail e senha são obrigatórios.'], 400);
}
if (!is_valid_email($email)) {
  json_response(['success' => false, 'message' => 'E-mail inválido.'], 400);
}

<<<<<<< HEAD
try {
  $pdo = getPDO();

  $st = $pdo->prepare('SELECT id, email, senha, tipo_usuario FROM usuarios WHERE email = :email LIMIT 1');
  $st->execute([':email' => $email]);
  $row = $st->fetch();

  if (!$row || !isset($row['senha']) || !password_verify($senha, (string)$row['senha'])) {
    json_response(['success' => false, 'message' => 'Credenciais inválidas.'], 401);
  }

  set_login_session($row);

  json_response([
    'success' => true,
    'message' => 'Login realizado com sucesso.',
    'user' => current_user(),
  ]);

} catch (Throwable $e) {
  json_response(['success' => false, 'message' => 'Erro no login.'], 500);
}
=======
$user = find_user_by_email($email);

if (!$user || !isset($user['password_hash']) || !password_verify($senha, (string)$user['password_hash'])) {
  json_response(['success' => false, 'message' => 'Credenciais inválidas.'], 401);
}

set_login_session($user);

json_response(['success' => true, 'message' => 'Login realizado com sucesso.', 'user' => current_user()]);
?>
>>>>>>> 07292da532e8a6136c47337d1511d7d2976f4e5c
