<?php 
    require_once './lib/mysql.php'; 
    $cart_items = $_SESSION['cart'] ?? []; // Получаем корзину из сессии. Это необязательно, но полезно, если нужно что-то проверить на PHP стороне.
?>

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Корзина</title>
        <link rel="icon" type="img/png" href="./img/favicon.png">
        <link rel="stylesheet" href="./css/main.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://kit.fontawesome.com/8e05dfae1d.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php require './blocks/header.php' ?>
        <main class="content container">
            <h2>🛒 Ваша корзина</h2>
            <div id="cart-container">
                <p>Загрузка данных корзины...</p>
            </div>
            <div id="cart-totals"></div>
            <p>
                <a href="index.php" class="back-link">← Продолжить покупки</a>
            </p>
        </main>
        <script src="./js/cart.js"></script>
        <script>
            loadCartDetails(); // Запускаем загрузку корзины при открытии страницы
        </script>
    </body>
</html>