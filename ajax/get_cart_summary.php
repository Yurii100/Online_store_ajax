<?php
require_once '../lib/mysql.php'; 

header('Content-Type: application/json');

$response = [
    'success' => true,
    'items' => [],
    'total_sum' => 0.00
];

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $response['success'] = true; // Корзина пуста - это не ошибка
    echo json_encode($response);
    exit;
}

$cart_items = $_SESSION['cart'];
$product_ids = array_keys($cart_items);

// 1. Получаем актуальные данные о товарах из БД
if (!empty($product_ids)) {
    // Создаем строку плейсхолдеров (?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR | PDO::FETCH_ASSOC);

        $total_sum = 0;
        
        foreach ($cart_items as $product_id => $quantity) {
            if (isset($products[$product_id])) {
                $product = $products[$product_id];
                $price = (float)$product['price'];
                $total_item_price = $price * $quantity;
                $total_sum += $total_item_price;
                
                $response['items'][] = [
                    'id' => (int)$product_id,
                    'title' => $product['title'],
                    'quantity' => (int)$quantity,
                    'price' => $price,
                    'total_price' => $total_item_price
                ];
            }
        }
        $response['total_sum'] = $total_sum;

    } catch (PDOException $e) {
        // Логирование ошибки и отправка отрицательного ответа
        error_log("DB Error in get_cart_summary: " . $e->getMessage());
        $response['success'] = false;
        $response['message'] = 'Ошибка при получении данных о товарах.';
    }
}

echo json_encode($response);