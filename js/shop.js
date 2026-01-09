$(document).ready(function() {
    let currentSortBy = 'created_at';
    let currentSortDir = 'DESC';
    let currentCategory = '';
    let currentSearch = '';
    let searchTimeout = null; // Переменная для хранения таймера (для Debouncing)

    function loadProducts(page, category='', sortBy='created_at', sortDir='DESC', searchQuery='') {
        // Устанавливаем Loader
        $('#products-container').html('<p>Загрузка товаров...</p>'); 
        
        $.ajax({
            url: 'ajax/load_products.php', 
            type: 'GET',
            data: { 
                page: page,
                category: category,
                sortBy: sortBy,  // <-- НОВЫЙ ПАРАМЕТР: Поле сортировки (price, title, created_at)
                sortDir: sortDir, // <-- НОВЫЙ ПАРАМЕТР: Направление (ASC или DESC) 
                searchQuery: searchQuery // <-- ПЕРЕДАЕМ ПАРАМЕТР ПОИСКА
            }, 
            dataType: 'json',
            success: function(response) {
                // Очищаем контейнеры
                $('#products-container').empty();
                $('#pagination-container').empty();
                
                // 1. Вывод товаров
                if (response.products.length > 0) {
                    $.each(response.products, (index, product) => {
                        // Генерируем HTML для каждого товара
                        let productHtml = `
                            <div class="product-item">
                                <a href="product.php?id=${product.id}" class="product-link">
                                    <img src="img/${product.img}" class="product-image">
                                    <h3>${product.title}</h3>
                                    <p>Цена: ${product.price} $.</p>
                                    <p>Категория: ${product.category}</p>
                                    <p>Описание: ${product.intro}</p>
                                </a>
                                <button class="add-to-cart-btn" data-product-id="${product.id}">🛒 В корзину</button>
                            </div>
                        `;
                        $('#products-container').append(productHtml);
                    });
                } else {
                    $('#products-container').html('<p>Товаров не найдено.</p>');
                }

                // 2. Вывод пагинации
                generatePagination(response.pagination.currentPage, response.pagination.totalPages, category);
            },
            error: (xhr, status, error) => {
                $('#products-container').html('<p class="error">Ошибка загрузки данных.</p>');
                console.error("AJAX Error:", status, error);
            }
        });
    };

    function loadCategories() {
        $.ajax({
            url: 'ajax/load_categories.php',
            type: 'GET',
            dataType: 'json',
            success: function(categories) {
                let categoriesHtml = '<span class="filter-label">Фильтр:</span>';
                
                $.each(categories, (index, item) => {
                    const categoryName = item.category;
                    const totalItems = item.total_items;
                    
                    categoriesHtml += `<a href="javascript:void(0);" class="category-link" data-category="${categoryName}">${categoryName}</a>`; // Создаем ссылку для каждой категории. data-category атрибут хранит имя категории для AJAX-запроса
                });
                
                $('#categories-container').append(categoriesHtml);
                
                $('#categories-container').on('click', '.category-link', function(e) {
                    e.preventDefault();
                    
                    // Снимаем класс 'active' со всех и устанавливаем его на кликнутую ссылку
                    $('#categories-container .category-link').removeClass('active');
                    $(this).addClass('active');

                    currentCategory = $(this).data('category'); // Получаем имя категории из data-атрибута
                    
                    if (currentCategory.toLowerCase() === 'all') { // Если выбрана "all", отправляем пустую строку или 'all', чтобы PHP-скрипт знал, что нужно сбросить WHERE-условие
                        currentCategory = ''; // Пустая строка - признак "ВСЕ" для PHP
                    };

                    currentSearch = $('#search-input').val() || '';
                    
                    loadProducts(1, currentCategory, currentSortBy, currentSortDir, currentSearch); // Запускаем загрузку товаров С ПЕРВОЙ СТРАНИЦЫ для новой категории
                });
                $('#categories-container .category-link[data-category="All"]').addClass('active'); // При первой загрузке делаем ссылку 'all' активной
            },
            error: function(xhr, status, error) {
                $('#categories-container').html('<p class="error">Ошибка загрузки фильтров.</p>');
                console.error("AJAX Error (Categories):", status, error);
            }
        });
    };

    function updateCartCount(count) { // Функция для обновления счетчика корзины
        $('#cart-count').text(count);
    };

    $(document).on('click', '.add-to-cart-btn', function() {
        const productId = $(this).data('product-id');
        const quantity = 1; // Пока что добавляем по 1

        $.ajax({
            url: 'ajax/add_to_cart.php',
            method: 'POST',
            data: { 
                product_id: productId,
                quantity: quantity 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateCartCount(response.total_items); // Обновляем счетчик товаров в шапке сайта
                    alert('Товар добавлен! Всего товаров: ' + response.total_items); // Оповещение пользователя (можно заменить модальным окном)
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function() {
                alert('Произошла ошибка при обращении к серверу.');
            }
        });
    });

    function generatePagination(currentPage, totalPages, category) {
        $('#pagination-container').data('category', category);
        let paginationHtml = '';
        const baseUrl = 'javascript:void(0);'; // Отмена перехода по ссылке

        if (totalPages > 1) { // Проверяем нужна ли пагниация
            if (currentPage > 1) { // Генерация кнопки "Предыдущая"
                paginationHtml += `<a href="${baseUrl}" data-page="${currentPage - 1}">&laquo; Предыдущая</a>`;
            }

            for (let i = 1; i <= totalPages; i++) { // Генерация номеров страниц
                let activeClass = (i === currentPage) ? 'active' : '';
                paginationHtml += `<a href="${baseUrl}" data-page="${i}" class="${activeClass}">${i}</a>`;
            }

            if (currentPage < totalPages) { // Генерация кнопки "Следующая"
                paginationHtml += `<a href="${baseUrl}" data-page="${currentPage + 1}">Следующая &raquo;</a>`;
            }
        };
        
        $('#pagination-container').append(paginationHtml);

        $('#pagination-container').off('click').on('click', 'a', function(e) {
            e.preventDefault(); // Останавливаем стандартное поведение ссылки
            let newPage = $(this).data('page'); // Получаем номер страницы из data-page по которой кликнули
            currentCategory = $(this).closest('#pagination-container').data('category');
            currentSearch = $('#search-input').val() || '';

            if (newPage) {
                loadProducts(newPage, currentCategory, currentSortBy, currentSortDir, currentSearch); // Загружаем новую страницу и категорию
            };
        });
    };

    function loadCheckoutSummary() {
        const $summaryContainer = $('#cart-summary-items');
        const $totalDisplay = $('#checkout-total');
        const $placeOrderBtn = $('#place-order-btn');
    
        // Сначала блокируем кнопку
        $placeOrderBtn.prop('disabled', true).text('Загрузка...');
        $summaryContainer.html('<p>Загрузка сводки заказа...</p>');

        $.ajax({
            url: 'ajax/get_cart_summary.php', // Мы создадим этот файл далее
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.items.length > 0) {
                    
                    // 1. Отображение списка товаров
                    let html = '<ul>';
                    response.items.forEach(item => {
                        html += `
                            <li>
                                ${item.title} (x${item.quantity}) — 
                                <strong>${item.total_price.toFixed(2)} USD.</strong>
                            </li>
                        `;
                    });
                    html += '</ul>';
                    
                    $summaryContainer.html(html);

                    // 2. Отображение общей суммы и разблокировка кнопки
                    $totalDisplay.text(response.total_sum.toFixed(2) + ' USD.');
                    $placeOrderBtn.prop('disabled', false).text('Подтвердить заказ');

                } else { // Корзина пуста
                    $summaryContainer.html('<p class="error-message">Корзина пуста. Добавьте товары, чтобы оформить заказ.</p>');
                    $totalDisplay.text('0.00 USD.');
                    $placeOrderBtn.prop('disabled', true).text('Корзина пуста');
                }
            },
            error: function() {
                $summaryContainer.html('<p class="error-message">Не удалось загрузить данные корзины.</p>');
                $placeOrderBtn.prop('disabled', true).text('Ошибка загрузки');
            }
        });
    };

    $('#sort-by').on('change', function() {
        // Получаем выбранный <option>
        let selectedOption = $(this).find('option:selected'); 
    
        // Извлекаем поле сортировки (value) и направление (data-dir)
        currentSortBy = selectedOption.val();
        currentSortDir = selectedOption.data('dir');

        // ОБНОВЛЯЕМ: Читаем и сохраняем текущее состояние фильтра и поиска
        currentCategory = $('#pagination-container').data('category') || '';
        currentSearch = $('#search-input').val() || '';
    
        // Загружаем товары с 1-й страницы с новыми параметрами сортировки
        loadProducts(1, currentCategory, currentSortBy, currentSortDir, currentSearch);
    });

    $('#search-input').on('input', function() { // ОБРАБОТЧИК ПОИСКА "НА ЛЕТУ"
        // 1. Очищаем предыдущий таймер, если он был установлен
        clearTimeout(searchTimeout);

        // 2. Получаем текущий текст из поля ввода
        currentSearch = $(this).val();

        // 3. Устанавливаем новый таймер (задержка 500 миллисекунд)
        searchTimeout = setTimeout(function() {
            // Читаем категорию из DOM (наиболее актуальный источник)
            currentCategory = $('#pagination-container').data('category') || '';

            // Вызываем загрузку товаров с новым запросом, сохраняя текущую сортировку и категорию
            loadProducts(1, currentCategory, currentSortBy, currentSortDir, currentSearch);
        }, 500); // Задержка в 0.5 секунды
    });

    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $('#place-order-btn');
        const $message = $('#checkout-message');
        
        $btn.prop('disabled', true).text('Обработка...');
        $message.html('');

        $.ajax({
            url: 'ajax/place_order.php', // Мы создадим этот файл далее
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $message.html(`<p class="success-message">🎉 Ваш заказ №${response.order_id} успешно оформлен!</p>`);
                    $form.trigger('reset'); // Очистка корзины и формы
                    loadCheckoutSummary(); // Перезагрузка сводки, чтобы показать пустую корзину
                } else {
                    $message.html('<p class="error-message">Ошибка: ' + (response.message || 'Неизвестная ошибка.') + '</p>');
                    $btn.prop('disabled', false).text('Повторить попытку');
                }
            },
            error: function() {
                $message.html('<p class="error-message">Ошибка связи с сервером при оформлении заказа.</p>');
                $btn.prop('disabled', false).text('Повторить попытку');
            }
        });
    });

    $(document).on('click', '.checkout-btn', function() {
        window.location.href = './checkout.php'; // Просто перенаправляем пользователя на страницу оформления заказа
    });

    loadCategories(); // Загрузка и привязка категорий
    loadProducts(1, currentCategory, currentSortBy, currentSortDir, currentSearch); // Первоначальная загрузка при открытии страницы (страница 1)
    loadCheckoutSummary();
});