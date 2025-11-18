$(document).ready(function() {
    $('#mess_send').on('click', function(e) {
        e.preventDefault(); // Предотвращаем стандартную отправку формы
        let username = $('#username').val();
        let email = $('#email').val();
        let message = $('#mess').val();

        let errorBlock = $('#error-block');
        errorBlock.text('').hide(); 
        
        // Простая валидация на клиенте (основная - на сервере)
        if (username.length === 0 || email.length === 0 || message.length === 0) {
            errorBlock.text('Пожалуйста, заполните все поля.').show();
            return false;
        };

        $.ajax({
            url: 'ajax/mail.php', 
            type: 'POST', 
            data: {
                username: username,
                email: email,
                mess: message // Имя поля должно совпадать с $_POST['mess'] в PHP
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message); 
                    $('form')[0].reset(); // Очистка формы
                    
                } else {
                    errorBlock.text(response.message).show();
                }
            },
            error: function(xhr, status, error) {
                errorBlock.text('Ошибка связи с сервером.').show();
                console.error("AJAX Error (Mail):", status, error);
            }
        });
    });
});