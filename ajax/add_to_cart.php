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

// 2. Инициализация корзины в сессии (если её нет)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 3. Обновление или добавление товара
if (isset($_SESSION['cart'][$product_id])) { // Товар уже есть: увеличиваем количество
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $_SESSION['cart'][$product_id] = [
                'quantity' => $quantity,
                'title' => $product['title'],
                'price' => $product['price']
            ];
        } else {
            $response['message'] = 'Товар не найден.';
            echo json_encode($response);
            exit();
        }
    } catch (PDOException $e) {
        // Обработка ошибки
        $response['message'] = 'Ошибка БД.';
        echo json_encode($response);
        exit();
    }
}

// 4. Расчет общего количества товаров в корзине
$total_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
}

// 5. Успешный ответ
$response['success'] = true;
$response['total_items'] = $total_items;
$response['message'] = 'Товар добавлен в корзину.';
echo json_encode($response);

?>