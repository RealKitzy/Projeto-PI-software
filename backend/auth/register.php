<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('POST');
<<<<<<< HEAD
$in = read_input();

$email  = normalize_email((string)($in['email'] ?? ''));
$senha  = (string)($in['senha'] ?? '');
$perfil = (string)($in['perfil'] ?? ($in['tipo_usuario'] ?? ''));

$tipo = map_tipo_usuario($perfil);

if ($email === '' || $senha === '' || !$tipo) {
  json_response(['success' => false, 'message' => 'Preencha e-mail, senha e perfil.'], 400);
=======
rate_limit('register', 10, 600);

$in = read_input();

$nome = safe_name((string)($in['nome'] ?? ''));
$email = normalize_email((string)($in['email'] ?? ''));
$perfil = (string)($in['perfil'] ?? '');
$senha = (string)($in['senha'] ?? '');
$confirmar = (string)($in['confirmarSenha'] ?? ($in['confirmar'] ?? ''));

if ($nome === '' || $email === '' || $perfil === '' || $senha === '' || $confirmar === '') {
  json_response(['success' => false, 'message' => 'Preencha todos os campos.'], 400);
>>>>>>> 07292da532e8a6136c47337d1511d7d2976f4e5c
}
if (!is_valid_email($email)) {
  json_response(['success' => false, 'message' => 'E-mail inválido.'], 400);
}
<<<<<<< HEAD
if (mb_strlen($senha) < 6) {
  json_response(['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres.'], 400);
}

try {
  $pdo = getPDO();

  $st = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
  $st->execute([':email' => $email]);
  if ($st->fetch()) {
    json_response(['success' => false, 'message' => 'Este e-mail já está cadastrado.'], 409);
  }

  $hash = password_hash($senha, PASSWORD_DEFAULT);

  $st = $pdo->prepare('
    INSERT INTO usuarios (email, senha, tipo_usuario)
    VALUES (:email, :senha, :tipo)
    RETURNING id, email, tipo_usuario, data_cadastro
  ');
  $st->execute([
    ':email' => $email,
    ':senha' => $hash,
    ':tipo'  => $tipo,
  ]);

  $user = $st->fetch();

  json_response([
    'success' => true,
    'message' => 'Cadastro realizado com sucesso. Faça login para continuar.',
    'user' => $user,
  ], 201);

} catch (Throwable $e) {
  json_response(['success' => false, 'message' => 'Erro no cadastro.'], 500);
}
=======
if (!is_valid_role($perfil)) {
  json_response(['success' => false, 'message' => 'Perfil inválido.'], 400);
}
if (mb_strlen($senha) < 6) {
  json_response(['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres.'], 400);
}
if (!hash_equals($senha, $confirmar)) {
  json_response(['success' => false, 'message' => 'As senhas não coincidem.'], 400);
}

$users = load_users();
foreach ($users as $u) {
  if (($u['email'] ?? '') === $email) {
    json_response(['success' => false, 'message' => 'Este e-mail já está cadastrado.'], 409);
  }
}

$user = [
  'id' => bin2hex(random_bytes(16)),
  'nome' => $nome,
  'email' => $email,
  'perfil' => $perfil,
  'password_hash' => password_hash($senha, PASSWORD_DEFAULT),
  'created_at' => gmdate('c'),
];

$users[] = $user;
save_users($users);

json_response(['success' => true, 'message' => 'Cadastro realizado com sucesso. Faça login para continuar.']);
?>
>>>>>>> 07292da532e8a6136c47337d1511d7d2976f4e5c
