<?php
header('Content-Type: application/json');

require_once '../lib/mysql.php'; 

$cart = $_SESSION['cart'] ?? [];

// Мы отправляем клиенту массив товаров в корзине (ключ: ID товара)
echo json_encode(['cart' => $cart]);