<?php
// 1. Подключение к базе данных (Предполагается, что у вас есть файл lib/mysql.php)
require_once '../lib/mysql.php'; 

// 2. Установка заголовков для AJAX-ответа
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Неизвестная ошибка.'];

// 3. Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Неверный метод запроса.';
    echo json_encode($response);
    exit();
};

// 4. Получение и базовая валидация данных
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['mess'] ?? '');

if (empty($username) || empty($email) || empty($message)) {
    $response['message'] = 'Все поля должны быть заполнены.';
    echo json_encode($response);
    exit();
};

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Email введен некорректно.';
    echo json_encode($response);
    exit();
};

try {
    // 5. SQL-запрос для сохранения данных
    $sql = "INSERT INTO messages (username, email, message_text) VALUES (:username, :email, :message)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':username' => htmlspecialchars($username),
        ':email' => htmlspecialchars($email),
        ':message' => htmlspecialchars($message)
    ]);

    // 6. Успешный ответ
    $response['success'] = true;
    $response['message'] = 'Сообщение успешно отправлено!';

} catch (PDOException $e) {
    // 7. Обработка ошибки базы данных. В продакшене лучше не показывать $e->getMessage()
    $response['message'] = 'Ошибка базы данных: ' . $e->getMessage(); 
};

echo json_encode($response);
exit();