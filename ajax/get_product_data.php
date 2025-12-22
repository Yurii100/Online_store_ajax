<?php
header('Content-Type: application/json');

// Включение отображения ошибок (только для отладки)
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../lib/mysql.php'; // Правильный путь к файлу подключения к БД

$product_id = (int)$_GET['id'] ?? 0;

if ($product_id === 0) {
    echo json_encode(['error' => 'Некорректный ID товара']);
    http_response_code(400); // Bad Request
    exit();
}

try { // Выбираем все необходимые поля
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $product_id]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) { // Успех: отправляем данные товара
        echo json_encode($product);
    } else { // Ошибка: товар не найден
        echo json_encode(['error' => 'Товар не найден']);
        http_response_code(404);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}