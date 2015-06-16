<?php

namespace Peach\Support;

class Str
{

    protected static $pluralNoChange = [
      'child',
      'fish',
      'deer',
      'sheep',
      'bread',
    ];
    protected static $pluralIrregular = [
      'man' => 'men',
      'woman' => 'women',
      'child' => 'children',
      'tooth' => 'teeth',
      'person' => 'people',
      'mouse' => 'mice',
    ];


    /**
     * Turns a string from underscored to camel case
     *
     * @param string $str
     * @return string
     */
    public static function cCase($str)
    {
        return preg_replace_callback('/_([A-Za-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $str);
    }

    /**
     * Turns a string from camel case to underscored
     *
     * @param string $str
     * @return string
     */
    public static function uCase($str)
    {
        $uCased = preg_replace_callback('/([A-Z])/', function ($matches) {
            return '_' . strtolower($matches[1]);
        }, $str);
        return strpos($uCased, '_') === 0 ? substr($uCased, 1) : $uCased;
    }

    /**
     * English _is_ predictable! mostly.
     *
     * Examples
     * ($x = 3).' '.Arr::pluralize($x, 'dog')                    # "3 dogs"
     * ($x = 1).' '.Arr::pluralize($x, 'banana')                 # "1 banana"
     * ($x = 6).' '.Arr::pluralize($x, 'moon', 'moons ago')      # "6 moons ago"
     * ($x = 1).' '.Arr::pluralize($x, 'moon', 'moons ago')      # "1 moon"
     * ($x = 5).' '.Arr::pluralize($x, 'deer')                   # "5 deer"
     *
     * @param integer $count
     * @param string $singleStr
     * @param null|string $pluralStr
     * @return null|string
     */
    public static function pluralize($count, $singleStr, $pluralStr = null)
    {
        if ($count == 1) {
            return $singleStr;
        }
        if ($pluralStr !== null) {
            return $pluralStr;
        }
        if (in_array($singleStr, self::$pluralNoChange)) {
            return $singleStr;
        }
        if (isset(self::$pluralIrregular[$singleStr])) {
            return self::$pluralIrregular[$singleStr];
        }

        $lastLetter = substr($singleStr, -1);
        switch ($lastLetter) {
            // sky => skies; library => libraries
            case 'y':
                return substr($singleStr, 0, -1) . 'ies';

            // half => halves; loaf => loaves
            case 'f':
                return substr($singleStr, 0, -1) . 'ves';

            // potato => potatoes; volcano => volcanoes
            case 'o':
                return $singleStr . 'es';
        }

        $lastLetters = substr($singleStr, -2);
        switch ($lastLetters) {
            // knife => knives; wife => wives
            case 'fe':
                return substr($singleStr, 0, -2) . 'ves';

            // cactus => cacti; nucleus => nuclei
            case 'us':
                return substr($singleStr, 0, -2) . 'i';

            // analysis => analyses; thesis => theses
            case 'is':
                return substr($singleStr, 0, -2) . 'es';

            // phenomenon => phenomena; criterion => criteria
            case 'on':
                return substr($singleStr, 0, -2) . 'a';
        }
        return $singleStr . 's';
    }
}