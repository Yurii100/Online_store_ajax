<?php
require_once '../lib/mysql.php'; 

header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 1) {
    http_response_code(403);
    echo '<p class="error-message">Доступ запрещен.</p>';
    exit;
}

$user_name = $_SESSION['user_name'] ?? '';

// Генерируем HTML-код раздела "Настройки"
echo '
    <h2>Настройки профиля</h2>
    <p>Вы можете изменить ваше имя и другую информацию ниже.</p>
    
    <form id="update-settings-form">
        <div class="form-group">
            <label for="setting-name">Имя:</label>
            <input type="text" id="setting-name" name="name" value="' . htmlspecialchars($user_name) . '" required>
        </div>
        
        <div class="form-group">
            <label for="setting-password">Новый пароль (оставьте пустым, чтобы не менять):</label>
            <input type="password" id="setting-password" name="new_password">
        </div>
        
        <button type="submit" class="auth-btn">Сохранить изменения</button>
        <p class="auth-message" id="settings-message"></p>
    </form>
';