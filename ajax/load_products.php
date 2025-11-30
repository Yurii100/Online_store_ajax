<?php
require_once '../lib/mysql.php';

$PER_PAGE = 5; // Сколько товаров на страницу

// Получение параметров сортировки и поиска
$sortBy = $_GET['sortBy'] ?? 'created_at'; 
$sortDir = strtoupper($_GET['sortDir'] ?? 'DESC');
$SEARCH_QUERY = (string)($_GET['searchQuery'] ?? '');

// Простая валидация для безопасности: разрешены только определенные поля
$allowedSortColumns = ['title', 'price', 'created_at'];
if (!in_array($sortBy, $allowedSortColumns)) {
    $sortBy = 'created_at'; // Устанавливаем безопасное значение по умолчанию
}

// Простая валидация для направления
if ($sortDir !== 'ASC' && $sortDir !== 'DESC') {
    $sortDir = 'DESC'; // Устанавливаем безопасное значение по умолчанию
}

// Получение текущей страницы из AJAX-запроса (GET)
$CURRENT_PAGE = (int)($_GET['page'] ?? 1);

// Проверка безопасности, $CURRENT_PAGE должен быть больше 0
if ($CURRENT_PAGE < 1) {
    $CURRENT_PAGE = 1;
}

$CATEGORY = (string)($_GET['category'] ?? '');

$OFFSET = ($CURRENT_PAGE - 1) * $PER_PAGE;

$WHERE_CLAUSE = '';
$SQL_PARAMS = [];
$conditions = [];

// Условие фильтрации по категории
if ($CATEGORY !== '') {
    $conditions[] = "category = :category";
    $SQL_PARAMS[':category'] = $CATEGORY;
}

// Условие поиска по тексту
if ($SEARCH_QUERY !== '') {
    $conditions[] = "(title LIKE :search OR description LIKE :search)"; // Используем оператор LIKE и два поля (title ИЛИ description)
    $SQL_PARAMS[':search'] = "%$SEARCH_QUERY%"; // Мы ищем строку, окруженную % (любые символы до и после)
}

// Объединяем все условия в WHERE CLAUSE
if (count($conditions) > 0) {
    $WHERE_CLAUSE = " WHERE " . implode(" AND ", $conditions);
}

// 5. Запрос общего количества товаров (для расчета общего числа страниц)
$sqlTotal = "SELECT COUNT(*) FROM products$WHERE_CLAUSE"; 
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($SQL_PARAMS);
$totalItems = $stmtTotal->fetchColumn(); 

$TOTAL_PAGES = ceil($totalItems / $PER_PAGE);

// Защита от несуществующей страницы
if ($CURRENT_PAGE > $TOTAL_PAGES && $TOTAL_PAGES > 0) {
    $CURRENT_PAGE = $TOTAL_PAGES;
    $OFFSET = ($CURRENT_PAGE - 1) * $PER_PAGE; // Пересчитываем OFFSET
}

// 6. Запрос данных для текущей страницы
$sqlProducts = "SELECT * FROM products$WHERE_CLAUSE ORDER BY $sortBy $sortDir LIMIT :limit OFFSET :offset"; 

$SQL_PARAMS[':limit'] = $PER_PAGE;
$SQL_PARAMS[':offset'] = $OFFSET;    
        
$stmtProducts = $pdo->prepare($sqlProducts);
$stmtProducts->execute($SQL_PARAMS);

$PRODUCTS = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

// 7. Формирование JSON-ответа
header('Content-Type: application/json');

$RESPONSE = [
    'products' => $PRODUCTS, // Сами данные товаров
    'pagination' => [
        'currentPage' => $CURRENT_PAGE,
        'totalPages' => (int)$TOTAL_PAGES,
    ]
];

echo json_encode($RESPONSE);
exit();