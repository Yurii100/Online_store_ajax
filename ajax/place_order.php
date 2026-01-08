<?php
require_once '../lib/mysql.php'; 

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Неизвестная ошибка.',
    'order_id' => null
];

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) { // 1. Проверка наличия товаров в корзине
    $response['message'] = 'Ваша корзина пуста. Нечего оформлять.';
    echo json_encode($response);
    exit;
}

// 2. Сбор данных из формы
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$city = trim($_POST['city'] ?? '');
$address = trim($_POST['address'] ?? '');
$comment = trim($_POST['comment'] ?? '');
$user_id = $_SESSION['user_id'] ?? null; // null, если не авторизован

if (empty($name) || empty($email) || empty($phone) || empty($city) || empty($address)) { // 3. Базовая валидация
    $response['message'] = 'Пожалуйста, заполните все обязательные поля (Имя, Email, Телефон, Город, Адрес).';
    echo json_encode($response);
    exit;
}

$cart_items = $_SESSION['cart'];
$product_ids = array_keys($cart_items);

// 4. Получение актуальных цен и расчет общей суммы
try {
    // Создаем строку плейсхолдеров
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, title, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR | PDO::FETCH_ASSOC);

    $final_order_items = [];
    $total_sum = 0;
    
    foreach ($cart_items as $product_id => $quantity) {
        if (isset($products[$product_id])) {
            $product = $products[$product_id];
            $price = (float)$product['price'];
            $total_sum += $price * $quantity;
            
            $final_order_items[] = [
                'product_id' => $product_id,
                'title' => $product['title'],
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }
    
    if (empty($final_order_items)) {
        $response['message'] = 'В корзине нет доступных товаров.';
        echo json_encode($response);
        exit;
    }

} catch (PDOException $e) {
    error_log("DB Error during order product check: " . $e->getMessage());
    $response['message'] = 'Ошибка базы данных при проверке товаров.';
    echo json_encode($response);
    exit;
}

// 5. Начало транзакции (для безопасности). Если один из запросов не пройдет, отменятся все.
$pdo->beginTransaction();

try {
    // 6. Запись в таблицу orders
    $sql = "INSERT INTO orders (user_id, total, status, name, email, phone, city, address, comment) VALUES (:user_id, :total, 'Новый', :name, :email, :phone, :city, :address, :comment)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':user_id' => $user_id,
        ':total' => $total_sum,
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':city' => $city,
        ':address' => $address,
        ':comment' => $comment,
    ]);
    
    $order_id = $pdo->lastInsertId();

    // 7. Запись в таблицу order_items
    $sql = "INSERT INTO order_items (order_id, product_id, title, price, quantity) VALUES (:order_id, :product_id, :title, :price, :quantity)";
    $stmt_item = $pdo->prepare($sql);
    
    foreach ($final_order_items as $item) {
        $stmt_item->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':title' => $item['title'],
            ':price' => $item['price'],
            ':quantity' => $item['quantity']
        ]);
    }
    
    // 8. Фиксация транзакции и очистка корзины
    $pdo->commit();
    $_SESSION['cart'] = []; // Очищаем корзину после успешного оформления

    $response['success'] = true;
    $response['message'] = 'Заказ успешно оформлен.';
    $response['order_id'] = $order_id;
    
} catch (PDOException $e) {
    // Если что-то пошло не так, откатываем все изменения
    $pdo->rollBack();
    error_log("DB Transaction failed: " . $e->getMessage());
    $response['message'] = 'Ошибка при сохранении заказа: ' . $e->getMessage();
}

echo json_encode($response);