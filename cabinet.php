<?php
    require_once './lib/mysql.php'; 

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] < 1) { // Проверка авторизации (остается без изменений)
        header('Location: index.php');
        exit;
    }

    $user_name = $_SESSION['user_name'] ?? 'Пользователь';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Рабочий Кабинет</title>
    <link rel="stylesheet" href="./css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/8e05dfae1d.js" crossorigin="anonymous"></script> 
</head>
<body>
    <?php require './blocks/header.php' ?>

    <main class="content container">
        <h1>Рабочий Кабинет</h1>

        <div class="cabinet-layout">

            <aside class="cabinet-nav">
                <a href="#" class="nav-item active" data-section="profile">Мой Профиль</a>
                <a href="#" class="nav-item" data-section="orders">Мои Заказы</a>
                <a href="#" class="nav-item" data-section="settings">Настройки</a>
            </aside>

            <section id="cabinet-content" class="cabinet-content">
                <p>Загрузка данных...</p>
            </section>
        </div>

        <p>
            <a href="index.php" class="back-link">← Продолжить покупки</a>
        </p>
    </main>
    <?php require './blocks/footer.php' ?>
    <script src="./js/auth.js"></script>
    <script src="./js/cabinet.js" defer></script> 
</body>
</html>