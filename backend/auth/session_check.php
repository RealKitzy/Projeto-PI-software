<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

require_method('GET');

$user = current_user();
if (!$user) {
  json_response(['success' => false, 'message' => 'Não autenticado.'], 401);
}

json_response(['success' => true, 'user' => $user]);
<<<<<<< HEAD
=======
?>
>>>>>>> 07292da532e8a6136c47337d1511d7d2976f4e5c
