<?php
// Base
declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$input = [];

if (stripos($contentType, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? [];
} else {
    $input = $_POST;
}

$email = trim((string)($input['email'] ?? ''));
$senha = (string)($input['senha'] ?? '');

if ($email === '' || $senha === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email e senha são obrigatórios.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email inválido.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getPDO();

    $sql = "SELECT id, nome, email, senha_hash, role, ativo
            FROM users
            WHERE email = :email
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($senha, $user['senha_hash'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Email ou senha incorretos.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!(bool)$user['ativo']) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Conta desativada.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'    => (int)$user['id'],
        'nome'  => $user['nome'],
        'email' => $user['email'],
        'role'  => $user['role'],
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso.',
        'user' => [
            'id'    => (int)$user['id'],
            'nome'  => $user['nome'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno no servidor.'
    ], JSON_UNESCAPED_UNICODE);
}