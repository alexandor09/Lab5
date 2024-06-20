<div class="login_container">
    <?php
    if ($isHeAuthorized) {
        echo "
        <h3>Вы авторизованы</h3>
        <span>". $_COOKIE['name']."</span>
        <span>". $_COOKIE['email']."</span>
        <form action='/pages/products.php' method='post'>
            <input type='submit' name='signOut' value='Выйти'>
        </form>
        ";
    } else {
        if ($error) {
            echo "<h3>$errorMsg</h3>";
        }
        echo "
                <h3>Авторизация</h3>
                <form  action='/pages/products.php' method='post'>
                    <div style='display: flex; justify-content: space-between'>
                        <label>Логин</label>
                        <input type='text' name='login' required>
                    </div>
                    <div style='display: flex; justify-content: space-between'>
                        <label>Пароль</label>
                        <input type='text' name='pwd' required>
                    </div>
                    <input type='submit' name='singIn' value='Войти'>
                </form>
                
                <h3>Регистрация</h3>
                <form action='/pages/products.php' method='post'>
                    <div style='display: flex; justify-content: space-between'>
                        <label>Имя</label>
                        <input type='text' name='name' required>
                    </div>
                    <div style='display: flex; justify-content: space-between'>
                        <label>Фамилия</label>
                        <input type='text' name='surname' required>
                    </div>
                    <div style='display: flex; justify-content: space-between'>
                        <label>Email</label>
                        <input type='email' name='email' required>
                    </div>
                    <div style='display: flex; justify-content: space-between'>
                        <label>Пароль</label>
                        <input type='text' name='pwd' required>
                    </div>
                    <input type='submit' name='singUp' value='Войти'>
                </form>
                
                ";
    }
    ?>
</div>