function loadProductDetails(id) {
    const container = $('#product-detail-container');

    $.ajax({
        url: 'ajax/get_product_data.php',
        method: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(product) {
            if (product.error) {
                container.html('<p class="error-message">' + product.error + '</p>');
                return;
            }

            let detailsHtml = `
                <div class="product-info-wrapper">
                    <div class="product-image-large">
                        <img src="img/${product.img}" alt="${product.title}">
                    </div>
                    
                    <div class="product-details">
                        <h1>${product.title}</h1>
                        
                        <p class="price">Цена: <span>${product.price} $</span></p>
                        <p class="category">Категория: ${product.category}</p>
                        
                        <div class="description-full">
                            <h2>Полное описание товара</h2>
                            <p>${product.description}</p>
                        </div>

                        <button class="buy-button" data-product-id="${product.id}">🛒 В корзину</button>
                    </div>
                </div>
                <a href="index.php" class="back-link">← Вернуться в каталог</a>
            `;

            container.html(detailsHtml);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            container.html('<p class="error-message">Ошибка загрузки деталей товара. Пожалуйста, попробуйте позже.</p>');
        }
    });
};

function updateCartCount(count) { // Функция для обновления счетчика корзины
    $('#cart-count').text(count);
};

$(document).ready(function() {
    $(document).on('click', '.buy-button', function() {
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
});