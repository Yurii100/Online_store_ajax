function loadCartDetails() { // Основная функция для загрузки и отображения корзины
    $.ajax({
        url: 'ajax/get_cart.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            renderCart(response.cart);
        },
        error: function() {
            $('#cart-container').html('<p class="error-message">Не удалось загрузить корзину. Попробуйте обновить страницу.</p>');
        }
    });
}

function renderCart(cartData) { // Функция для отрисовки корзины (генерация HTML)
    const $container = $('#cart-container');
    const $totals = $('#cart-totals');
    
    if (Object.keys(cartData).length === 0) { // Проверка, пуста ли корзина
        $container.html('<p class="info-message">Ваша корзина пуста. Начните покупки!</p>');
        $totals.empty();
        return;
    }

    let cartHtml = `
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    `;
    let overallTotal = 0;

    // Цикл по всем товарам в корзине
    for (const productId in cartData) {
        const item = cartData[productId];
        const subtotal = item.quantity * item.price;
        overallTotal += subtotal;

        cartHtml += `
            <tr data-id="${productId}">
                <td>${item.title}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <input type="number" class="item-quantity" value="${item.quantity}" min="1" data-id="${productId}">
                </td>
                <td class="item-subtotal">$${subtotal.toFixed(2)}</td>
                <td>
                    <button class="remove-item-btn" data-id="${productId}">Удалить</button>
                </td>
            </tr>
        `;
    }

    cartHtml += `
            </tbody>
        </table>
    `;
    
    // Вставляем таблицу в контейнер
    $container.html(cartHtml);

    // Расчет итогов
    const totalsHtml = `
        <div class="cart-summary">
            <h3>Итого: $${overallTotal.toFixed(2)}</h3>
            <button class="checkout-btn">Оформить заказ</button>
        </div>
    `;
    $totals.html(totalsHtml);
}