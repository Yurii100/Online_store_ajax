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

                        <button class="buy-button">Добавить в корзину</button>
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
}