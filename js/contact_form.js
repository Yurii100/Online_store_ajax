$(document).ready(function() {
    $('#mess_send').on('click', function(e) {
        e.preventDefault(); // Предотвращаем стандартную отправку формы

        // 1. Собираем данные формы
        let username = $('#username').val();
        let email = $('#email').val();
        let message = $('#mess').val();

        // 2. Очищаем блок ошибок и скрываем его
        let errorBlock = $('#error-block');
        errorBlock.text('').hide(); 
        
        // Простая валидация на клиенте (основная - на сервере)
        if (username.length === 0 || email.length === 0 || message.length === 0) {
            errorBlock.text('Пожалуйста, заполните все поля.').show();
            return false;
        };

        // 3. AJAX-запрос
        $.ajax({
            url: 'ajax/mail.php', 
            type: 'POST', // Используем POST для отправки данных формы
            data: {
                username: username,
                email: email,
                mess: message // Имя поля должно совпадать с $_POST['mess'] в PHP
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Успех: Выводим сообщение и очищаем форму
                    alert(response.message); 
                    $('form')[0].reset(); // Очистка формы
                    
                } else {
                    // Ошибка: Выводим сообщение об ошибке
                    errorBlock.text(response.message).show();
                }
            },
            error: function(xhr, status, error) {
                // Общая ошибка AJAX-запроса (например, 500 Internal Server Error)
                errorBlock.text('Ошибка связи с сервером.').show();
                console.error("AJAX Error (Mail):", status, error);
            }
        });
    });
});