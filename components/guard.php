<?php
$allowedPaths = array(
    'admin' => ['basket.php', 'orders.php', 'products.php', 'user_info.php', 'admin.php', 'confirmed.php'],
    'manager' => ['basket.php', 'orders.php', 'products.php', 'user_info.php', 'confirmed.php'],
    'worker' => ['basket.php', 'orders.php', 'products.php', 'user_info.php'],
    'customer' => ['basket.php', 'orders.php', 'products.php', 'user_info.php'],

);

if (!isset($_COOKIE['user_role']) && !str_ends_with($_SERVER['REQUEST_URI'], "products.php")) {
    header("Location: http://" . $_SERVER['HTTP_HOST'] . '/pages/products.php');
    die();
}

if (isset($_COOKIE['user_role'])) {
    if (isset($allowedPaths[$_COOKIE['user_role']])) {
        $paths = $allowedPaths[$_COOKIE['user_role']];
        $flag = false;
        foreach ($paths as $path) {
            $flag = str_ends_with($_SERVER['REQUEST_URI'], $path);
            if ($flag) break;
        }
        if (!$flag) {
            header("Location: http://" . $_SERVER['HTTP_HOST'] . '/pages/products.php');
            die();
        }
    } else {
        header("Location: http://" . $_SERVER['HTTP_HOST'] . '/pages/products.php');
        die();
    }
}