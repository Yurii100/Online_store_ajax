<?php $product_id = (int)$_GET['id'] ?? 0; ?>;

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> | Магазин</title>
    <link rel="icon" type="img/png" href="./img/favicon.png">
    <link rel="stylesheet" href="./css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/8e05dfae1d.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php require './blocks/header.php' ?>

    <main class="content container">
        <div id="product-detail-container">
            <p>Загрузка информации о товаре...</p>
        </div>
    </main>

    <script src="./js/product.js"></script> 

    <script>
        const PRODUCT_ID = <?php echo json_encode($product_id); ?>;
        loadProductDetails(PRODUCT_ID);
    </script>
</body>
</html>