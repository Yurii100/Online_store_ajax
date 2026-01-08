<?php
require_once '../lib/mysql.php'; 

header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 1) {
    http_response_code(403);
    echo '<p class="error-message">Доступ запрещен.</p>';
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = [];

try { // Получаем все заказы пользователя
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :id ORDER BY created_at DESC");
    $stmt->execute([':id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("DB Error fetching orders: " . $e->getMessage());
    echo '<p class="error-message">Ошибка базы данных при загрузке заказов.</p>';
    exit;
}

echo '
    <h2>Мои Заказы</h2>
    <p>История всех ваших покупок в магазине.</p>
    
    <table class="orders-table">
        <thead>
            <tr>
                <th>Номер заказа</th>
                <th>Дата</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Действие</th>
            </tr>
        </thead>
        <tbody>
';

if (empty($orders)) {
    echo '
        <tr>
            <td colspan="5" style="text-align: center; padding: 20px;">
                <p>История заказов пока пуста. <a href="index.php">Начать покупки</a></p>
            </td>
        </tr>
    ';
} else {
    foreach ($orders as $order) {
        $date = (new DateTime($order['created_at']))->format('d.m.Y H:i'); // Форматирование даты
        
        // Отображение строки заказа
        echo '
            <tr>
                <td># ' . htmlspecialchars($order['id']) . '</td>
                <td>' . $date . '</td>
                <td>' . number_format($order['total'], 2, ',', ' ') . ' USD.</td>
                <td><span class="status-' . strtolower($order['status']) . '">' . htmlspecialchars($order['status']) . '</span></td>
                <td><a href="#" class="view-order-details" data-order-id="' . $order['id'] . '">Подробнее</a></td>
            </tr>
        ';
    }
}

echo '
        </tbody>
    </table>
';
