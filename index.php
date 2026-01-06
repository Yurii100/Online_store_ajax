<?php require_once './lib/mysql.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Online_store_ajax</title>
        <link rel="icon" type="img/png" href="./img/favicon.png">
        <link rel="stylesheet" href="./css/main.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://kit.fontawesome.com/8e05dfae1d.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php require './blocks/header.php' ?>
        <main class="content container">
            <div class="main-controls">
                <div class="site-info">
                    <img src="./img/favicon.png" alt="Логотип" class="logo"> 
                    <span class="slogan">Мы знаем, что вам нужно</span>
                </div>
                <div class="search-controls">
                    <input type="text" id="search-input" placeholder="Введите название товара...">
                </div>  
                <div class="user-auth-area">
                    <?php 
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) { // Если пользователь авторизован:
                            echo '<a href="#" class="user-cabinet-link">Рабочий кабинет</a>';
                            echo ' | <a href="#" id="logout-link">Выход</a>'; 
                        } else { // Если пользователь не авторизован:
                            echo '<a href="#" id="login-link">Вход</a>';
                            echo ' | <a href="#" id="register-link">Регистрация</a>';
                        }
                    ?>
                </div>               
            </div>
            <h1>Наши Товары</h1>    
            <div id="categories-container" class="categories"></div>
            <div id="sort-container" class="sort-controls">
                <label for="sort-by">Сортировать по:</label>
                <select id="sort-by">
                    <option value="created_at" data-dir="DESC">Дате (Новые)</option>
                    <option value="created_at" data-dir="ASC">Дате (Старые)</option>
                    <option value="price" data-dir="ASC">Цене (По возрастанию)</option>
                    <option value="price" data-dir="DESC">Цене (По убыванию)</option>
                    <option value="title" data-dir="ASC">Алфавиту (А-Я)</option>
                    <option value="title" data-dir="DESC">Алфавиту (Я-А)</option>
                </select>
            </div>
            <div id="products-container" class="products"></div>
            <div id="pagination-container" class="pagination"></div>
        </main>
        <?php require './blocks/modals.php' ?>
        <?php require './blocks/footer.php' ?>
        <script src="./js/shop.js"></script>
        <script src="./js/auth.js"></script>
    </body>
</html>