<?php
require_once '../lib/mysql.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'reload_required' => false];

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 1) {
    $response['message'] = 'Необходимо авторизоваться.';
    http_response_code(403);
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$new_name = trim($_POST['name'] ?? '');
$new_password = $_POST['new_password'] ?? '';

if (empty($new_name)) {
    $response['message'] = 'Имя не может быть пустым.';
    echo json_encode($response);
    exit;
}

$fields_to_update = [];
$params = [];
$needs_reload = false;

if ($new_name !== $_SESSION['user_name']) { // 1. Обновление имени
    $fields_to_update[] = "name = :name";
    $params[':name'] = $new_name;
    $needs_reload = true;
}

if (!empty($new_password)) { // 2. Обновление пароля (если указан)
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $fields_to_update[] = "password = :password";
    $params[':password'] = $hashed_password;
}

if (!empty($fields_to_update)) { // Если есть что обновлять
    $sql = "UPDATE users SET " . implode(", ", $fields_to_update) . " WHERE id = :id";
    $params[':id'] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            if ($needs_reload) { // Если имя изменилось, обновляем сессию
                $_SESSION['user_name'] = $new_name;
                $response['reload_required'] = true;
            }
            $response['success'] = true;
            $response['message'] = 'Настройки успешно сохранены.';
        } else {
            $response['message'] = 'Не удалось обновить данные.';
        }
    } catch (PDOException $e) {
        error_log("Settings update error: " . $e->getMessage());
        $response['message'] = 'Ошибка базы данных при сохранении.';
    }
} else {
    $response['success'] = true;
    $response['message'] = 'Нет изменений для сохранения.';
}

echo json_encode($response);