<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты</title>
    <link rel="stylesheet" href="./css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/8e05dfae1d.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php require './blocks/header.php' ?>
    <div>
        <h1>Контакты</h1>
        <form> 
            <label for="username">Ваше имя: </label>
            <input type="text" name="username" id="username">

            <label for="email">Ваш email: </label>
            <input type="email" name="email" id="email">

            <label for="mess">Сообщение: </label>
            <textarea name="mess" id="mess"></textarea>

            <div class="error-mess" id="error-block"></div>

            <button type="button" id="mess_send">Отправить</button>
        </form>
    </div>
    <script src="./js/contact_form.js"></script>
</body>
</html>