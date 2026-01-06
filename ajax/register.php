<?php
header('Content-Type: application/json');
require_once '../lib/mysql.php';

$response = ['success' => false, 'message' => ''];

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    $response['message'] = 'Заполните все поля.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Некорректный формат email.';
    echo json_encode($response);
    exit;
}

try {
    // 1. Проверяем, существует ли пользователь с таким email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->fetch()) {
        $response['message'] = 'Пользователь с таким email уже зарегистрирован.';
        echo json_encode($response);
        exit;
    }

    // 2. Хешируем пароль
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Добавляем пользователя в базу
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    if ($stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed_password])) {
        // Успех: Автоматически авторизуем пользователя
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        
        $response['success'] = true;
        $response['message'] = 'Регистрация прошла успешно. Вы авторизованы.';
    } else {
        $response['message'] = 'Ошибка при добавлении пользователя.';
    }

} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    $response['message'] = 'Ошибка базы данных.';
}

echo json_encode($response);