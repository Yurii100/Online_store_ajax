<?php
header('Content-Type: application/json');
require_once '../lib/mysql.php';

$response = ['success' => false, 'message' => ''];

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $response['message'] = 'Заполните все поля.';
    echo json_encode($response);
    exit;
}

try {
    // 1. Ищем пользователя по email
    $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // 2. Пароль совпал: Устанавливаем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        
        $response['success'] = true;
        $response['message'] = 'Вход успешен!';
    } else {
        // 3. Пароль или email неверны
        $response['message'] = 'Неверный email или пароль.';
    }

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    $response['message'] = 'Ошибка базы данных.';
}

echo json_encode($response);