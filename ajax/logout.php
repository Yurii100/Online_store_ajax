<?php
require_once '../lib/mysql.php'; 

header('Content-Type: application/json');

$response = ['success' => false];

if (isset($_SESSION['user_id'])) { // Проверяем, авторизован ли пользователь вообще
    $_SESSION = []; // 1. Очищаем все переменные сессии
    
    session_destroy(); // 2. Уничтожаем саму сессию на сервере
    
    if (ini_get("session.use_cookies")) { // 3. Также очищаем cookie сессии на стороне клиента (не обязательно, но хорошая практика)
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    $response['success'] = true;
} else {
    $response['success'] = true; 
}

echo json_encode($response);