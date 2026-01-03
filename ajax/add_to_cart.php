<?php
header('Content-Type: application/json');
require_once '../lib/mysql.php'; // Подключаем сессии и БД

$response = ['success' => false, 'message' => '', 'total_items' => 0];

// 1. Получаем данные
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

if ($product_id === 0 || $quantity < 1) { // Проверка на корректность и безопасность данных.
    $response['message'] = 'Некорректные данные.';
    echo json_encode($response);
    exit();
}

// 2. Инициализация корзины
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 3. Получаем актуальные данные о товаре 
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $response['message'] = 'Товар не найден.';
        echo json_encode($response);
        exit();
    }
} catch (PDOException $e) { // Обработка ошибки
    $response['message'] = 'Ошибка БД при получении данных о товаре.';
    echo json_encode($response);
    exit();
}

$available_stock = (int)($product['stock'] ?? 0); // Получаем остаток со склада
$current_cart_quantity = $_SESSION['cart'][$product_id]['quantity'] ?? 0; // Получаем текущее количество товара в корзине
$new_total_quantity = $current_cart_quantity + $quantity; // Сколько всего товаров будет в корзине, если мы добавим запрошенное количество

// 4. Проверка остатков
if ($available_stock < 1) { // Проверка 1: Товар полностью закончился
    if (isset($_SESSION['cart'][$product_id])) { // Если товара нет, но он был в корзине (старый товар) - удаляем его
        unset($_SESSION['cart'][$product_id]);
    }
    $response['message'] = 'Извините, товар закончился.';
    echo json_encode($response);
    exit;
}

if ($new_total_quantity > $available_stock) { // Проверка 2: Превышение остатка
    $final_quantity = $available_stock; // Устанавливаем максимальное разрешенное количество
    
    if ($current_cart_quantity < $available_stock) { // Если товар уже был в корзине и пользователь пытается добавить еще:
         $response['message'] = "Добавлено только до максимального остатка {$available_stock} шт.";
    } else {
        $response['message'] = "Вы уже достигли максимального остатка ({$available_stock} шт.). Добавление невозможно.";
    }
    
} else { // Если все в порядке, берем полное запрошенное количество
    $final_quantity = $new_total_quantity;
}

// 5. Обновление сессии 
$_SESSION['cart'][$product_id] = [
    'title' => $product['title'], 
    'price' => $product['price'],
    'quantity' => $final_quantity // Используем количество, прошедшее валидацию
];

// 6. Расчет общего количества товаров в корзине
$total_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
}

// 7. Успешный ответ
$response['success'] = true;
$response['total_items'] = $total_items;

if (empty($response['message'])) {
    $response['message'] = 'Товар добавлен в корзину.';
}

echo json_encode($response);