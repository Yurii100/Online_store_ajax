<?php
header('Content-Type: application/json');

require_once '../lib/mysql.php'; 

function getProductStockFromDB($product_id) {
    global $pdo; // Используем глобальный объект $pdo, созданный в mysql.php

    if (!$pdo) { // Защита, если $pdo вдруг не был инициализирован
        error_log("Ошибка: Объект PDO не доступен.");
        return false;
    }

    try {
        $sql = "SELECT price, stock FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([':id' => $product_id]);
        
        $product_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $product_data;
        
    } catch (PDOException $e) { // Обработка ошибок базы данных
        error_log("DB Error in getProductStockFromDB: " . $e->getMessage());
        return false;
    }
}

$response = ['success' => false, 'message' => '', 'new_subtotal' => 0, 'new_total' => 0, 'total_items' => 0];

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if ($product_id === 0 || $quantity < 1) {
    $response['message'] = 'Некорректные данные.';
    $response['actual_quantity'] = 1;
    echo json_encode($response);
    exit();
}

// 1. Получаем остаток со склада 
$product_data = getProductStockFromDB($product_id); 
$available_stock = (int)($product_data['stock'] ?? 0);

// --- 2. ВАЛИДАЦИЯ КОЛИЧЕСТВА ---
if ($available_stock < 1) {
    $response['message'] = 'Товар закончился. Он будет удален из корзины.';
    $quantity = 0; // Принудительное удаление
} else if ($quantity > $available_stock) {
    $quantity = $available_stock; // Ограничиваем количество остатком
    $response['message'] = "Максимальное количество товара на складе: {$available_stock} шт. Количество ограничено.";
}

// 3. Обновление корзины
if (isset($_SESSION['cart'][$product_id])) { // 1. Проверяем наличие товара
    if ($quantity === 0) { 
        unset($_SESSION['cart'][$product_id]); // Если количество 0, удаляем товар
    } else { 
        $_SESSION['cart'][$product_id]['quantity'] = $quantity; // Обновляем количество
    }

    $overall_total = 0;
    $total_items = 0;
    
    if ($quantity > 0) { // В случае если $quantity > 0: пересчитываем необходимые значения
        $item_price = $_SESSION['cart'][$product_id]['price'];
        $new_subtotal = $quantity * $item_price;
    } else {
        $new_subtotal = 0; // Для удаленного товара
    }
    
    foreach ($_SESSION['cart'] as $item) {
        $overall_total += $item['quantity'] * $item['price'];
        $total_items += $item['quantity'];
    }
    
    // 4. Успешный ответ
    $response['success'] = true;
    $response['new_subtotal'] = number_format($new_subtotal, 2, '.', ''); // Форматируем для JS
    $response['new_total'] = number_format($overall_total, 2, '.', '');
    $response['total_items'] = $total_items;
    
} else {
    $response['message'] = 'Товар не найден в корзине.';
}

$response['actual_quantity'] = $quantity; // ВАЖНО: Возвращаем фактическое количество, установленное сервером

echo json_encode($response);