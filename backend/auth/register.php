<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('POST');
$in = read_input();

$email  = normalize_email((string)($in['email'] ?? ''));
$senha  = (string)($in['senha'] ?? '');
$perfil = (string)($in['perfil'] ?? ($in['tipo_usuario'] ?? ''));

$tipo = map_tipo_usuario($perfil);

if ($email === '' || $senha === '' || !$tipo) {
  json_response(['success' => false, 'message' => 'Preencha e-mail, senha e perfil.'], 400);
}
if (!is_valid_email($email)) {
  json_response(['success' => false, 'message' => 'E-mail inválido.'], 400);
}
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
