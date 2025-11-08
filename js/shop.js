$(document).ready(function() {
    function loadProducts(page, category='') {
        // Устанавливаем Loader
        $('#products-container').html('<p>Загрузка товаров...</p>'); 
        
        $.ajax({
            url: 'ajax/load_products.php', 
            type: 'GET',
            data: { // Передаем номер страницы и категорию на сторону сервера для sql-запроса в базу данных.
                page: page,
                category: category 
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
                                <h3>${product.title}</h3>
                                <p>Цена: ${product.price} $.</p>
                                <p>Категория: ${product.category}</p>
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

                    let selectedCategory = $(this).data('category'); // Получаем имя категории из data-атрибута

                    console.log('Категория:', selectedCategory, 'Тип:', typeof selectedCategory);
                    
                    if (selectedCategory.toLowerCase() === 'all') { // Если выбрана "all", отправляем пустую строку или 'all', чтобы PHP-скрипт знал, что нужно сбросить WHERE-условие
                        selectedCategory = ''; // Пустая строка - признак "ВСЕ" для PHP
                    };
                    
                    loadProducts(1, selectedCategory); // Запускаем загрузку товаров С ПЕРВОЙ СТРАНИЦЫ для новой категории
                });
                $('#categories-container .category-link[data-category="All"]').addClass('active'); // При первой загрузке делаем ссылку 'all' активной
            },
            error: function(xhr, status, error) {
                $('#categories-container').html('<p class="error">Ошибка загрузки фильтров.</p>');
                console.error("AJAX Error (Categories):", status, error);
            }
        });
    };

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
            let currentCategory = $(this).closest('#pagination-container').data('category');

            if (newPage) {
                loadProducts(newPage, currentCategory); // Загружаем новую страницу и категорию
            };
        });
    };

    loadCategories(); // Загрузка и привязка категорий
    loadProducts(1, ''); // Первоначальная загрузка при открытии страницы (страница 1)
});