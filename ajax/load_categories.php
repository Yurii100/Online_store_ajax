<?php
require_once '../lib/mysql.php';

// 1. Запрос уникальных категорий и подсчет товаров в каждой. Использование GROUP BY обеспечивает получение уникального списка
$sql = "SELECT category, COUNT(id) as total_items 
        FROM products 
        GROUP BY category 
        ORDER BY category ASC";
                
$stmt = $pdo->prepare($sql);
$stmt->execute();

$CATEGORIES = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Добавление опции "Все товары" (это важно для сброса фильтра)
array_unshift($CATEGORIES, [
    'category' => 'All', 
    'total_items' => array_sum(array_column($CATEGORIES, 'total_items'))
]);

// 3. Формирование JSON-ответа
header('Content-Type: application/json');
echo json_encode($CATEGORIES);
exit();
