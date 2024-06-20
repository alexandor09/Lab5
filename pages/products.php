<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Список товаров</title>
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
        <form method="post" action="/pages/products.php">
            <?php
            $productTypeList = DBClass::getProductTypeList();
            if (count($productTypeList) > 0) {
                array_unshift($productTypeList, array('id' => 0, 'name' => 'Всё'));
                echo "<select name='prod_type'>";
                foreach ($productTypeList as $type) {
                    if (isset($_POST['prod_type']) && $_POST['prod_type'] === $type['name']) {
                        echo "<option value='" . $type['name'] . "' selected>" . $type['name'] . "</option>";
                    } else {
                        echo "<option value=" . $type['name'] . ">" . $type['name'] . "</option>";
                    }
                }
                echo "</select>";
                echo "
            <input type='submit' value='Отфильтровать по сложности'>
            ";
            }
            ?>
        </form>
        <div class="main_table">
            <?php
            if (isset($_POST['basket'])) {
                if (!isset($_SESSION['id_list']) || !is_array($_SESSION['id_list']))
                    $_SESSION['id_list'] = array();

                if (isset($_SESSION['id_list'][$_POST['id']]))
                    $_SESSION['id_list'][$_POST['id']] += $_POST['amount'];
                else
                    $_SESSION['id_list'][$_POST['id']] = $_POST['amount'];
                unset($_POST['basket']);
            }

            if (isset($_POST['remove'])) {
                DBClass::removeProductById($_POST['id']);
                if (isset($_SESSION['id_list'])) {
                    $bad_key = null;
                    foreach ($_SESSION['id_list'] as $key => $item) {
                        if ($key === $_POST['id']) {
                            $bad_key = $key;
                            break;
                        }
                    }
                    if (isset($bad_key))
                        unset($_SESSION['id_list'][$bad_key]);
                }
                unset($_POST['id']);
            }

            /** @var ProductEntity[] $productList */
            $productList = array();
            $listOfRoles = array('admin', 'customer', 'manager');
            $listOfRolesWithoutCustomer = array('admin', 'manager');

            if (isset($_POST['prod_type'])) {
                $prod_type = $_POST['prod_type'];
                if ($prod_type === 'Всё')
                    $productList = DBClass::getProductList();
                else
                    $productList = DBClass::getProductListByFilter($prod_type);
            } else {
                $productList = DBClass::getProductList();
            }
            echo "
            <table>
                <tr>
                    <th>Наименование продукта</th>
                    <th>Сложность реализации</th>
                    <th>Нужна ли комада</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    
                  ";
            if (isset($_COOKIE['user_role']) && in_array($_COOKIE['user_role'], $listOfRoles)) {
                echo "
                    <th>Кол-во</th>
                    <th>В корзину</th>
                ";
            }
            if (isset($_COOKIE['user_role']) && in_array($_COOKIE['user_role'], $listOfRolesWithoutCustomer)) {
                echo "<th>Удалить</th>";
            }
            echo "</tr>";
            foreach ($productList as $prod) {
                echo "
                <tr>
                    <form action='/pages/products.php' method='post'>
                        <td><input style='display: none' type='number' name='id' value=" . $prod->getId() . ">
                        " . $prod->getName() . "</td>
                        <td>" . $prod->getProductType() . "</td>
                        <td>" . $prod->getSex() . "</td>
                        <td>" . $prod->getDescription() . "</td>
                        <td>" . $prod->getPrice() . "</td>";
                        
                if (isset($_COOKIE['user_role']) && in_array($_COOKIE['user_role'], $listOfRoles)) {
                    echo "
                    <td><input style='max-width:70px;'  type='number' min='1' name='amount' value=1></td>
                    <td><input class='basket_btn' type='submit' name='basket' value='Добавить'></td>
                    ";
                }
                if (isset($_COOKIE['user_role']) && in_array($_COOKIE['user_role'], $listOfRolesWithoutCustomer)) {
                    echo "<td><input class='remove_btn' type='submit' name='remove' value='Удалить'></td>";
                }
                echo "
                    </form>
                </tr>
                ";
            }
            echo "</table>";
            
            ?>
        </div>
    </div>
</div>
</body>
<?php
    
?>
</html>
