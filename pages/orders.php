<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Заказы</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
<?php
require_once '../components/header.php';
require_once '../DB/DBClass.php';
DBClass::connect();

// Проверка авторизации пользователя
if (!isset($_COOKIE['id'])) {
    echo "<div class='main_container'><h3>Пожалуйста, авторизуйтесь, чтобы увидеть ваши заказы.</h3></div>";
    exit();
}

$userId = $_COOKIE['id'];
$orders = DBClass::getUserOrders($userId);


if (isset($_POST['create_order'])){
    DBClass::createOrder();
}

?>
<div class="container">
    <?php require_once '../components/login.php'; ?>
    <div class="main_container">
        <h1>Ваши заказы</h1>
        <?php
        if (empty($orders)) {
            echo "<p>У вас нет заказов.</p>";
        } else {
            echo "<table border='1'>
                    <tr>
                        <th>ID Заказа</th>
                        <th>Дата создания</th>
                        <th>Подтверждено</th>
                        <th>Товары</th>
                    </tr>";
            $currentOrderId = null;
            foreach ($orders as $order) {
                if ($currentOrderId !== $order['order_id']) {
                    if ($currentOrderId !== null) {
                        echo "</td></tr>";
                    }
                    $currentOrderId = $order['order_id'];
                    echo "<tr>
                            <td>{$order['order_id']}</td>
                            <td>{$order['create_date']}</td>
                            <td>" . ($order['confirmed'] ? 'Да' : 'Нет') . "</td>
                            <td>";
                }
                echo "<div>{$order['product_name']} ({$order['amount']} шт.) - " . ($order['product_price'] * $order['amount']) . " руб.</div>";
            }
            echo "</td></tr></table>";
        }
        ?>
    </div>
</div>
</body>
</html>
