<div id="login-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Вход в систему</h2>
        <form id="login-form">
            <div class="form-group">
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Пароль:</label>
                <input type="password" id="login-password" name="password" required>
            </div>
            <button type="submit" class="auth-btn">Войти</button>
            <p class="auth-message" id="login-message"></p>
        </form>
    </div>
</div>

<div id="register-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Регистрация</h2>
        <form id="register-form">
            <div class="form-group">
                <label for="register-name">Имя:</label>
                <input type="text" id="register-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="register-email">Email:</label>
                <input type="email" id="register-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="register-password">Пароль:</label>
                <input type="password" id="register-password" name="password" required>
            </div>
            <button type="submit" class="auth-btn">Зарегистрироваться</button>
            <p class="auth-message" id="register-message"></p>
        </form>
    </div>
</div>