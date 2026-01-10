$(document).ready(function() {
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
    };

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

        for (const productId in cartData) { // Цикл по всем товарам в корзине
            const item = cartData[productId];
            const subtotal = item.quantity * item.price;
            overallTotal += subtotal;

            cartHtml += `
                <tr data-id="${productId}">
                    <td>${item.title}</td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td>
                        <input type="number" class="item-quantity" value="${item.quantity}" min="1" max="${item.stock}" data-id="${productId}">
                    </td>
                    <td class="item-subtotal">$${subtotal.toFixed(2)}</td>
                    <td>
                        <button id="remove-product-${productId}" class="cart-action-btn" data-id="${productId}">Удалить</button>
                    </td>
                </tr>
            `;
        }

        cartHtml += `
                </tbody>
            </table>
        `;
        
        $container.html(cartHtml); // Вставляем таблицу в контейнер

        // Расчет итогов
        const totalsHtml = `
            <div class="cart-summary">
                <h3>Итого: $${overallTotal.toFixed(2)}</h3>
                <button class="checkout-btn">Оформить заказ</button>
            </div>
        `;
        $totals.html(totalsHtml);
    };

    function updateCartCount(count) { 
        $('#cart-count').text(count); 
    };

    $(document).on('click', '[id^="remove-product-"]', function() { // Обработчик клика для удаления одного товара
        const productId = $(this).data('id');
        const $row = $(this).closest('tr'); 

        $.ajax({
            url: 'ajax/remove_item.php',
            method: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $row.remove(); // 1. Удаляем строку товара из DOM
                    
                    updateCartCount(response.total_items); // 2. Обновляем счетчик в шапке
                    
                    loadCartDetails(); // 3. Пересчитываем общую сумму и перерисовываем итоги, для этого просто вызываем главную функцию загрузки (она все обновит).
                } else {
                    alert('Ошибка удаления: ' + response.message);
                }
            },
            error: function() {
                alert('Ошибка связи с сервером при удалении.');
            }
        });
    });

    $(document).on('click', '#clear-cart-btn', function() { // Обработчик клика для кнопки "Удалить всё"
        if (!confirm('Вы уверены, что хотите очистить всю корзину?')) {
            return; 
        }

        $.ajax({
            url: 'ajax/clear_cart.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateCartCount(0); // Обновляем счетчик на 0
                    loadCartDetails(); // Перезагружаем корзину (она покажет, что корзина пуста)
                } else {
                    alert('Ошибка очистки: ' + response.message);
                }
            },
            error: function() {
                alert('Ошибка связи с сервером при очистке корзины.');
            }
        });
    });


    $(document).on('change', '.item-quantity', function() { // Обработчик изменения количества товара
        const $input = $(this);
        const productId = $input.data('id');
        let quantity = parseInt($input.val());

        if (quantity < 1 || isNaN(quantity)) { // Клиентская валидация (если пользователь ввел ноль или отрицательное)
            quantity = 1;
            $input.val(1);
        }

        const maxQuantity = parseInt($input.attr('max'));
        if (maxQuantity && quantity > maxQuantity) {
            alert(`Максимальный остаток на складе: ${maxQuantity} шт.`);
            quantity = maxQuantity;
            $input.val(maxQuantity);
        }

        $.ajax({
            url: 'ajax/update_quantity.php',
            method: 'POST',
            data: { 
                product_id: productId,
                quantity: quantity 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const $row = $input.closest('tr');

                    if (typeof response.actual_quantity !== 'undefined' && parseInt($input.val()) !== response.actual_quantity) { // НОВАЯ ЛОГИКА: Обновление поля ввода, если сервер ограничил количество
                        $input.val(response.actual_quantity); // Устанавливаем в поле ввода то количество, которое разрешил сервер

                        if(response.message) { // Показываем сообщение, если сервер его прислал (например, об ограничении)
                            alert('Внимание: ' + response.message);
                        }
                    }
                    
                    $row.find('.item-subtotal').text('$' + response.new_subtotal); // 1. Обновляем сумму для этого товара
                    
                    $('#cart-totals h3').text('Итого: $' + response.new_total); // 2. Обновляем ОБЩУЮ сумму корзины
                    
                    updateCartCount(response.total_items); // 3. Обновляем счетчик в шапке
                } else {
                    alert('Ошибка обновления: ' + response.message);
                    
                    loadCartDetails(); // Если ошибка, перезагружаем корзину, чтобы восстановить старое значение
                }
            },
            error: function() {
                alert('Ошибка связи с сервером при обновлении количества.');
            }
        });
    });

    loadCartDetails();
});