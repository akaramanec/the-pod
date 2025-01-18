<?php

namespace src\validators;

use yii\validators\RegularExpressionValidator;

class OnlyRussianLetters extends RegularExpressionValidator
{
    public $pattern = "/^[0-9А-Яа-яЇїЁёІіъЪЄє ' -]+$/isu";
    public $message = 'Допускаются только символы кириллицы';
}
