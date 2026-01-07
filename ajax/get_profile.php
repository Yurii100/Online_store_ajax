<?php
require_once '../lib/mysql.php';

header('Content-Type: text/html; charset=utf-8'); 

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 1) { // Проверка авторизации!
    http_response_code(403);
    echo '<p class="error-message">Доступ запрещен. Пожалуйста, авторизуйтесь.</p>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Неизвестно';

// Получение данных пользователя из БД
try {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_email = $user_data['email'] ?? 'N/A';
} catch (PDOException $e) {
    error_log("DB Error fetching user data: " . $e->getMessage());
    $user_email = 'Ошибка базы данных';
}

// Генерируем HTML-код раздела "Мой Профиль"
echo '
    <h2>Мой Профиль</h2>
    <p>Здесь вы можете просмотреть и изменить свои персональные данные.</p>
    
    <div class="profile-info">
        <p><strong>Имя:</strong> ' . htmlspecialchars($user_name) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($user_email) . '</p>
    </div>
    
    <div class="cabinet-actions">
        <button id="edit-profile-btn" class="btn btn-secondary">Редактировать</button>
    </div>
';