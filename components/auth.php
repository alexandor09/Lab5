<?php
$isHeAuthorized = false;
$errorMsg = "";
$error = false;

$authorization = function (string $userName, string $pwd) {
    global $isHeAuthorized;
    global $errorMsg;
    global $error;
    require '../exceptions/UserNotExist.php';
    try {
        if (DBClass::setUserToCookie($userName, $pwd)) {
            $isHeAuthorized = true;
            $error = false;
        } else {
            $isHeAuthorized = false;
        }
    } catch (UserNotExist $e) {
        $errorMsg = "Пользователь с таким именем ли паролем не существует";
        $error = true;
    }
};

$registration = function ($name, $surname, $password, $email) {
    global $isHeAuthorized;
    global $errorMsg;
    global $error;
    if (DBClass::registerNewUser($name, $surname, $password, $email)) {
        $isHeAuthorized = true;
        $error = false;
    } else {
        $isHeAuthorized = false;
        $error = true;
        $errorMsg = "Ошибка при регистрации";
    }
};

if (isset($_POST["login"]) && isset($_POST["pwd"]) && isset($_POST["singIn"])) {
    $authorization(htmlentities($_POST["login"]), htmlentities($_POST["pwd"]));
}

if (
    isset($_POST["name"]) &&
    isset($_POST["surname"]) &&
    isset($_POST["email"]) &&
    isset($_POST["pwd"]) &&
    isset($_POST["singUp"])) {
    $registration(
        htmlentities($_POST["name"]),
        htmlentities($_POST["surname"]),
        htmlentities($_POST["email"]),
        htmlentities($_POST["pwd"])
    );
}

if (isset($_POST["signOut"])) {
    require '../utils/Cookie.php';
    Cookie::removeUserCookies();
    $isHeAuthorized = false;
    $error = false;
}

if (isset($_COOKIE['name'])) {
    $isHeAuthorized = true;
} else {
    $isHeAuthorized = false;
}

require '../components/guard.php';