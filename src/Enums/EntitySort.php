<?php

namespace App\Enums;

enum EntitySort: int
{
    case EMAIL = 1;
    case FIRSTNAME = 2;
    case LASTNAME = 3;
    case BIRTHDAY = 4;
    case PESEL = 5;
    case GENDER = 6;
}