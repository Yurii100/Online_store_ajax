<?php require_once './lib/mysql.php'; ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заказа</title>
    <link rel="stylesheet" href="./css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/shop.js"></script> </head>
<body>
    <?php require './blocks/header.php' ?>

    <main class="content container">

        <h1>Оформление заказа</h1>
        
        <form id="checkout-form" class="checkout-grid">
            
            <section class="checkout-section contact-info">

                <h2>1. Ваши данные</h2>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="alert-info">
                        Вы не авторизованы. <a href="#" id="login-link">Войдите</a> или <a href="#" id="register-link">зарегистрируйтесь</a>, чтобы сохранить данные.
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="order-name">Имя:</label>
                    <input type="text" id="order-name" name="name" required value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="order-email">Email:</label>
                    <input type="email" id="order-email" name="email" required value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="order-phone">Телефон:</label>
                    <input type="tel" id="order-phone" name="phone" placeholder="+7 (XXX) XXX-XX-XX" required>
                </div>

            </section>

            <section class="checkout-section delivery-info">

                <h2>2. Доставка</h2>

                <div class="form-group">
                    <label for="order-city">Город:</label>
                    <input type="text" id="order-city" name="city" required>
                </div>

                <div class="form-group">
                    <label for="order-address">Улица, дом, квартира:</label>
                    <input type="text" id="order-address" name="address" required>
                </div>

                <div class="form-group">
                    <label for="order-comment">Комментарий к заказу (необязательно):</label>
                    <textarea id="order-comment" name="comment"></textarea>
                </div>

            </section>
            
            <section class="checkout-section order-summary">

                <h2>3. Ваш заказ</h2>

                <div id="cart-summary-items">
                    <p>Корзина пуста. Добавьте товары, чтобы оформить заказ.</p>
                </div>

                <div class="total-line">
                    <strong>Итого к оплате:</strong> <span id="checkout-total">0.00 USD.</span>
                </div>
                
                <button type="submit" id="place-order-btn" class="auth-btn" disabled>Подтвердить заказ</button>

                <p id="checkout-message"></p>

            </section>

        </form>
        
    </main>
    <?php require './blocks/modals.php' ?>
    <?php require './blocks/footer.php' ?>
    <script src="./js/shop.js"></script>
    <script src="./js/auth.js"></script>
</body>
</html>