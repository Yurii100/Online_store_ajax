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
    }

    $(document).on('click', '.cabinet-nav .nav-item', function(e) { // 1. Обработчик клика по меню навигации
        e.preventDefault();
        const $this = $(this);
        const section = $this.data('section');
        
        // Устанавливаем активный класс
        $('.cabinet-nav .nav-item').removeClass('active');
        $this.addClass('active');
        
        loadCabinetSection(section); // Загружаем новый раздел
    });

    loadCabinetSection('profile');
});