<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('GET');

$user = current_user();
if (!$user) {
  json_response(['success' => false, 'message' => 'Não autenticado.'], 401);
}

json_response(['success' => true, 'user' => $user]);
