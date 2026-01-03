<?php
header('Content-Type: application/json');

require_once '../lib/mysql.php';

$response = ['success' => false, 'message' => '', 'total_items' => 0];

$product_id = (int)($_POST['product_id'] ?? 0);

if ($product_id === 0) {
    $response['message'] = 'Неверный ID товара.';
    echo json_encode($response);
    exit();
}

if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$product_id])) { // Проверяем, существует ли корзина и есть ли в ней этот товар
    
    unset($_SESSION['cart'][$product_id]); // Удаляем элемент из массива корзины
    
    $response['success'] = true;
    $response['message'] = 'Товар успешно удален.';
} else {
    $response['message'] = 'Товар не найден в корзине.';
}

// Расчет общего количества (для обновления счетчика в шапке)
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }
}
$response['total_items'] = $total_items;

echo json_encode($response);