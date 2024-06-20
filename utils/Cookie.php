<?php

class Cookie
{
    public static function setCookies(
        string $id,
        string $name,
        string $surname,
        string $email,
        string $user_role): void
    {
        setcookie('id', $id, 0, "/");
        setcookie('name', $name, 0, "/");
        setcookie('surname', $surname, 0, "/");
        setcookie('email', $email, 0, "/");
        setcookie('user_role', $user_role, 0, "/");

        $_COOKIE['id'] = $id;
        $_COOKIE['name'] = $name;
        $_COOKIE['surname'] = $surname;
        $_COOKIE['email'] = $email;
        $_COOKIE['user_role'] = $user_role;
    }

    public static function removeUserCookies(): void
    {
        setcookie('id', '', 1, "/");
        setcookie('name', '', 1, "/");
        setcookie('surname', '', 1, "/");
        setcookie('email', '', 1, "/");
        setcookie('user_role', '', 1, "/");
        unset($_COOKIE['id']);
        unset($_COOKIE['name']);
        unset($_COOKIE['surname']);
        unset($_COOKIE['email']);
        unset($_COOKIE['user_role']);
    }
}