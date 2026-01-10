<?php
require_once '../lib/mysql.php'; 

header('Content-Type: application/json');

$total_count = 0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_count += $item['quantity']; // Считаем общее количество всех товаров в корзине
    }
}

echo json_encode(['count' => $total_count]);