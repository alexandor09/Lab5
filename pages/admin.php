<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Админ</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
<?php
require_once '../components/header.php';
require_once '../DB/DBClass.php';
DBClass::connect();

// Проверка авторизации администратора
if (!isset($_COOKIE['user_role']) || $_COOKIE['user_role'] !== 'admin') {
    echo "<div class='main_container'><h3>Пожалуйста, войдите как администратор, чтобы управлять заказами.</h3></div>";
    exit();
}

// Обработка формы для подтверждения и отмены подтверждения заказа
if (isset($_POST['confirm_order'])) {
    $orderId = $_POST['order_id'];
    DBClass::confirmOrder($orderId);
}
if (isset($_POST['unconfirm_order'])) {
    $orderId = $_POST['order_id'];
    DBClass::unconfirmOrder($orderId);
}

// Получение всех заказов
$allOrders = DBClass::getAllOrders();
?>
<div class="container">
    <?php require_once '../components/login.php'; ?>
    <div class="main_container">
        <h1>Все заказы</h1>
        <?php
        if (empty($allOrders)) {
            echo "<p>Нет заказов.</p>";
        } else {
            echo "<table border='1'>
                    <tr>
                        <th>ID Заказа</th>
                        <th>Дата создания</th>
                        <th>Подтверждено</th>
                        <th>Товары</th>
                        <th>Действие</th>
                    </tr>";
            $currentOrderId = null;
            foreach ($allOrders as $order) {
                if ($currentOrderId !== $order['order_id']) {
                    if ($currentOrderId !== null) {
                        echo "</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='order_id' value='$currentOrderId'>
                                        <input type='submit' name='confirm_order' value='Подтвердить'>
                                        <input type='submit' name='unconfirm_order' value='Отменить подтверждение'>
                                    </form>
                                </td>
                              </tr>";
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
            echo "</td>
                    <td>
                        <form method='post' action=''>
                            <input type='hidden' name='order_id' value='$currentOrderId'>
                            <input type='submit' name='confirm_order' value='Подтвердить'>
                            <input type='submit' name='unconfirm_order' value='Отменить подтверждение'>
                        </form>
                    </td>
                  </tr></table>";
        }
        ?>
    </div>
</div>
</body>
</html>
