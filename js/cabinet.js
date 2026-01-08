$(document).ready(function() { // 2. Инициализация: Загрузка раздела 'profile' при первой загрузке страницы
    function loadCabinetSection(sectionName) { // Функция для динамической загрузки содержимого кабинета
        const $contentContainer = $('#cabinet-content');
        
        $contentContainer.html('<p class="loading-message">Загрузка раздела ' + sectionName + '...</p>'); // Показываем индикатор загрузки
        
        let url;
        
        switch (sectionName) {
            case 'profile':
                url = 'ajax/get_profile.php';
                break;
            case 'orders':
                url = 'ajax/get_orders.php'; // Нужно будет создать позже
                break;
            case 'settings':
                url = 'ajax/get_settings.php'; // Нужно будет создать позже
                break;
            default:
                $contentContainer.html('<p class="error-message">Раздел не найден.</p>');
                return;
        }

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html', // Мы ожидаем, что сервер вернет готовый HTML 
            success: function(htmlResponse) {
                $contentContainer.html(htmlResponse);
            },
            error: function() {
                $contentContainer.html('<p class="error-message">Не удалось загрузить данные для раздела "' + sectionName + '".</p>');
            }
        });
    };

    $(document).on('click', '.cabinet-nav .nav-item', function(e) { // 1. Обработчик клика по меню навигации
        e.preventDefault();
        const $this = $(this);
        const section = $this.data('section');
        
        // Устанавливаем активный класс
        $('.cabinet-nav .nav-item').removeClass('active');
        $this.addClass('active');
        
        loadCabinetSection(section); // Загружаем новый раздел
    });

    $(document).on('submit', '#update-settings-form', function(e) { // Обработчик сохранения настроек (появится после загрузки контента)
        e.preventDefault();
        const $form = $(this);
        const $message = $('#settings-message');
        
        $message.text('Сохранение...');
        
        $.ajax({
            url: 'ajax/update_settings.php', // Нам нужно будет создать этот файл!
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $message.text(response.message).css('color', 'green');
                    
                    if (response.reload_required) { // Обновляем имя в сессии и перезагружаем профиль, чтобы увидеть изменения
                        setTimeout(function() {
                            loadCabinetSection('profile'); // Перезагружаем профиль для отображения нового имени
                        }, 1000);
                    }
                } else {
                    $message.text(response.message).css('color', 'red');
                }
            },
            error: function() {
                $message.text('Ошибка связи с сервером.').css('color', 'red');
            }
        });
    });

    loadCabinetSection('profile');
});