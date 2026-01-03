<?php
header('Content-Type: application/json');

require_once '../lib/mysql.php'; 

$response = ['success' => true, 'message' => 'Корзина очищена.', 'total_items' => 0];

if (isset($_SESSION['cart'])) { // Просто удаляем массив корзины из сессии
    unset($_SESSION['cart']);
}

echo json_encode($response);