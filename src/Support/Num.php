<?php

namespace Peach\Support;

class Num
{

    public static function isEven($num)
    {
        return !($num % 2) || $num == 0;
    }

    public static function isOdd($num)
    {
        return !static::isEven($num);
    }

}