<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('POST');
rate_limit('register', 10, 600);

$in = read_input();

$nome = safe_name((string)($in['nome'] ?? ''));
$email = normalize_email((string)($in['email'] ?? ''));
$perfil = (string)($in['perfil'] ?? '');
$senha = (string)($in['senha'] ?? '');
$confirmar = (string)($in['confirmarSenha'] ?? ($in['confirmar'] ?? ''));

if ($nome === '' || $email === '' || $perfil === '' || $senha === '' || $confirmar === '') {
  json_response(['success' => false, 'message' => 'Preencha todos os campos.'], 400);
}
if (!is_valid_email($email)) {
  json_response(['success' => false, 'message' => 'E-mail inválido.'], 400);
}
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