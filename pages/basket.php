<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Корзина</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
<?php
require '../components/header.php';
?>
<div class="container">
    <?php
    require '../components/login.php'
    ?>
    <div class="main_container">

        <div class="main_table">
            <?php
            if (isset($_SESSION['id_list']) && count($_SESSION['id_list']) > 0) {
                if (isset($_POST['remove'])) {
                    $_SESSION['id_list'][$_POST['id']] -= $_POST['amount'];

                    if ($_SESSION['id_list'][$_POST['id']] < 1)
                        unset($_SESSION['id_list'][$_POST['id']]);
                }
                $productList = DBClass::getProductByIds(array_keys($_SESSION['id_list']));
                echo "
                <form action='/pages/orders.php' method='post'>
                    <input type='submit' name='create_order' value='Создать заказ'>
                </form>
                <table>
                    <tr>
                        <th>Наименование продукта</th>
                        <th>Тип продукта</th>
                        <th>Пол</th>
                        <th>Описание</th>
                        <th>Стоимость</th>
                        <th>Количество</th>
                        <th>Удалить</th>
                    </tr>
                ";
                foreach ($productList as $prod) {
                    echo "
                    <tr>
                        <form id='saveForm2' action='/pages/basket.php' method='post'>
                            <td><input style='display: none' type='number' name='id' value=" . $prod->getId() . ">
                            " . $prod->getName() . "</td>
                            <td>" . $prod->getProductType() . "</td>
                            <td>" . $prod->getSex() . "</td>
                            <td>" . $prod->getDescription() . "</td>
                            <td>" . $prod->getPrice() . "</td>
                            <td>
                                <input 
                                    type='number'
                                    min='0'
                                    name='amount'
                                    max=" . $_SESSION['id_list'][$prod->getId()] . "
                                    value=" . $_SESSION['id_list'][$prod->getId()] . ">
                            </td>
                            <td><input class='remove_btn' type='submit' name='remove' value='Удалить'></td>
                        </form>
                    </tr>
                    ";
                }
                echo "</table>";
            } else {
                echo "<h3>Корзина пуста</h3>";
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
