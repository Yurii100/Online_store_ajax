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

if (!empty($product_ids)) { // 1. Получаем актуальные данные о товарах из БД
    $placeholders = implode(',', array_fill(0, count($product_ids), '?')); // Создаем строку плейсхолдеров (?, ?, ?)
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($product_ids);
        $raw_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = []; // Новый массив, где ID будет ключом

        foreach ($raw_products as $product) {
            $products[$product['id']] = $product;
        }

        $total_sum = 0;
        
        foreach ($cart_items as $product_id => $item_data) {
            $quantity = (int)($item_data['quantity'] ?? 0); // Получаем количество ИЗ ВЛОЖЕННОГО МАССИВА

            if (isset($products[$product_id]) && $quantity > 0) {
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