<?php

class UserNotExist extends Exception
{
    function __construct($username)
    {
        parent::__construct("Пользователь с таким именем $username не существует");
    }
}