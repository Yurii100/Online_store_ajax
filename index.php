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
        <main class="content">
            <h1>Наши Товары</h1>
            <div class="search-controls">
                <label for="search-input">Поиск по названию/описанию:</label>
                <input type="text" id="search-input" placeholder="Введите название товара...">
            </div>     
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
        <script src="./js/shop.js"></script>
    </body>
</html>