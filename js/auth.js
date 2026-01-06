function openModal(modalId) { // Открывает модальное окно
    $(modalId).css('display', 'block');
}

function closeModal(modalId) { // Закрывает модальное окно
    $(modalId).css('display', 'none');
    // Очищаем сообщения и поля при закрытии
    $(modalId + ' form')[0].reset(); 
    $(modalId + ' .auth-message').text('');
}

// -------------------------------------
// 1. ОБРАБОТЧИКИ ОТКРЫТИЯ/ЗАКРЫТИЯ
// -------------------------------------

// Открытие модальных окон
$(document).on('click', '#login-link', function(e) {
    e.preventDefault();
    openModal('#login-modal');
});

$(document).on('click', '#register-link', function(e) {
    e.preventDefault();
    openModal('#register-modal');
});

// Закрытие при клике на 'x'
$('.close-btn').on('click', function() {
    closeModal($(this).closest('.modal').attr('id') === 'login-modal' ? '#login-modal' : '#register-modal');
});

// Закрытие при клике вне окна
$(window).on('click', function(event) {
    if ($(event.target).is('.modal')) {
        closeModal('#login-modal');
        closeModal('#register-modal');
    }
});

// -------------------------------------
// 2. ОБРАБОТЧИКИ ФОРМ (AJAX)
// -------------------------------------

$('#login-form').on('submit', function(e) { // Обработка формы ВХОДА
    e.preventDefault();
    const $form = $(this);
    const $message = $('#login-message');
    
    $message.text('Загрузка...');
    
    $.ajax({
        url: 'ajax/login.php',
        method: 'POST',
        data: $form.serialize(), // Собираем данные формы
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $message.text(response.message).css('color', 'green');
                setTimeout(function() { // Даем 1 секунду, чтобы увидеть сообщение, затем перезагружаем
                    window.location.reload(); 
                }, 1000);
            } else {
                $message.text(response.message).css('color', 'red');
            }
        },
        error: function() {
            $message.text('Ошибка связи с сервером.').css('color', 'red');
        }
    });
});

$('#register-form').on('submit', function(e) { // Обработка формы РЕГИСТРАЦИИ
    e.preventDefault();
    const $form = $(this);
    const $message = $('#register-message');
    
    $message.text('Загрузка...');

    $.ajax({
        url: 'ajax/register.php',
        method: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $message.text(response.message).css('color', 'green');
                 setTimeout(function() { // Даем 1 секунду, чтобы увидеть сообщение, затем перезагружаем
                    window.location.reload(); 
                }, 1000);
            } else {
                $message.text(response.message).css('color', 'red');
            }
        },
        error: function() {
            $message.text('Ошибка связи с сервером.').css('color', 'red');
        }
    });
});