<div>
    <div class="header"></div>
    <div class="nav_bar">
        <nav>
            <ul>
                <li><a href="/pages/products.php">Продукты</a></li>
                <?php
                session_start();
                require_once '../DB/DBClass.php';
                DBClass::connect();
                require '../components/auth.php';
                if(isset($_COOKIE['user_role'])){
                    echo "
                    <li><a href='/pages/user_info.php'>Информация о пользователе</a></li>
                    <li><a href='/pages/orders.php'>Заказы</a></li>
                    <li><a href='/pages/confirmed.php'>Подтверждение</a></li>
                    <li><a href='/pages/basket.php'>Корзина</a></li>
                    ";
                }
                ?>

                <?php
                    if(isset($_COOKIE['user_role']) && $_COOKIE['user_role'] === 'admin'){
                        echo "<li><a href='/pages/admin.php'>Админ</a></li>";
                    }
                ?>
            </ul>
        </nav>
    </div>
</div>