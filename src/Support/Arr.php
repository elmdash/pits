<?php

namespace Peach\Support;

class Arr
{

    const KEY_ONLY = 1;
    const VAL_ONLY = 2;
    const KEY_VAL  = 4;

    // MISC  ------------------------------------------------------------------------------------

    /**
     * Returns an array with the default values being overwritten by any values in the $overrides array.
     * Modifies the original array.
     *
     * @param array $defaults
     * @param array $overrides
     * @param bool $strict If strict, then only the keys in $defaults will be merged from overrides
     */
    public static function defaults(array &$defaults, array $overrides = [], $strict = false)
    {
        if (!count($overrides)) {
            return;
        }
        if ($strict) {
            $overrides = static::filterByKeys($overrides, array_keys($defaults));
        }
        $defaults = array_merge($defaults, $overrides);
    }

    /**
     * Converts array keys from underscore to lower camel case.
     *
     * @param array $array
     */
    public static function keysUnderscoreToLowerCamelCase(array &$array)
    {
        $keys = array_keys($array);
        array_walk($keys, function (&$x) {
            $x = Str::cCase($x);
        });
        $array = array_combine($keys, array_values($array));
    }


    /**
     * Apply a function to all keys of an array.
     *
     * Because `array_walk_recursive` does not allow altering keys
     *
     * @param array $array
     * @param callable $func Returns a new key
     */
    public static function walkKeysRecursive(array &$array, callable $func)
    {
        $out = [];
        foreach ($array as $key => $val) {
            $newKey = $func($key);
            if (is_array($val)) {
                static::walkKeysRecursive($val, $func);
            }
            $out[$newKey] = $val;
        }
        $array = $out;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function flatten(array $array) {
        $out = [];
        array_walk_recursive($array, function($a) use (&$out) { $out[] = $a; });
        return $out;
    }

    /**
     * Apply a method to each object in an array.
     *
     * @param array $array
     * @param string $method
     * @param array $args
     * @return array
     */
    public static function mapMethod(array $array, $method, $args = [])
    {
        return array_map(function($obj) use ($method, $args) {
            return call_user_func_array([$obj, $method], $args);
        }, $array);
    }



    // FINDING ----------------------------------------------------------------------------

    /**
     * Remove and return values from an array using a callable.
     *
     * - If $limit is greater than 0, then only that many results can be returned.
     * - If $where return -1, then that breaks the loop and returns early.
     * - If $where returns true or equivalent to true, then that value is taken from the
     *   given $array and added to the result set.
     *
     * @param array $array
     * @param callable $where
     * @param int $limit
     * @return array
     */
    public static function spliceWhere(array &$array, callable $where, $limit = -1)
    {
        $results = [];
        $found = 0;
        foreach ($array as $key => $val) {
            if ($limit >= 0 && $found == $limit) {
                break;
            }
            $res = $where($val, $key);
            if ($res === -1) {
                break;
            }
            if ($res) {
                $results[$key] = $val;
                ++$found;
                unset($array[$key]);
            }
        }
        return $results;
    }

    /**
     * Returns the first value where the callable returns true.
     *
     * Callable takes $val, $key as args unless specified differently with a flag.
     *
     * @param array $array
     * @param callable $where
     * @param int $callableArgs Which arguments the callable takes
     * @param mixed $notFoundValue Which value to return to signify no value was found.
     * @return mixed
     */
    public static function find(array $array, callable $where, $callableArgs = Arr::KEY_VAL, $notFoundValue = -1)
    {
        switch ($callableArgs) {
            case Arr::KEY_ONLY:
                foreach ($array as $key => $val) {
                    if ($where($key)) {
                        return $val;
                    }
                }
                break;
            case Arr::VAL_ONLY:
                foreach ($array as $key => $val) {
                    if ($where($val)) {
                        return $val;
                    }
                }
                break;
            case Arr::KEY_VAL:
                foreach ($array as $key => $val) {
                    if ($where($val, $key)) {
                        return $val;
                    }
                }
                break;
        }

        return $notFoundValue;
    }

    /**
     * Returns the first value where the callable returns false.
     *
     * Callable takes $val, $key as args unless specified differently with a flag.
     *
     * @param array $array
     * @param callable $where
     * @param int $callableArgs Which arguments the callable takes
     * @param mixed $notFoundValue Which value to return to signify no value was found.
     * @return mixed
     */
    public static function findNot(array $array, callable $where, $callableArgs = Arr::KEY_VAL, $notFoundValue = -1)
    {
        switch ($callableArgs) {
            case Arr::KEY_ONLY:
                foreach ($array as $key => $val) {
                    if (!$where($key)) {
                        return $val;
                    }
                }
                break;
            case Arr::VAL_ONLY:
                foreach ($array as $key => $val) {
                    if (!$where($val)) {
                        return $val;
                    }
                }
                break;
            case Arr::KEY_VAL:
                foreach ($array as $key => $val) {
                    if (!$where($val, $key)) {
                        return $val;
                    }
                }
                break;
        }

        return $notFoundValue;
    }

    /**
     * Returns a new array with only the elements with the given keys.
     * Associations are preserved.
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function filterByKeys(array $array, array $keys)
    {
        return array_intersect_key($array, array_fill_keys($keys, null));
    }

    /**
     * Returns a new array with only the elements with the given keys.
     * Associations are preserved.
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function removeByKeys(array $array, array $keys)
    {
        return array_diff_key($array, array_fill_keys($keys, null));
    }

    /**
     * Returns the first value where the callable returns true.
     *
     * Callable only takes $key as argument.
     * Only works if no values are -1.
     *
     * @param array $array
     * @param callable $where
     * @return int|bool FALSE if $where is not callable, -1 if not found
     */
    public static function findWithKey(array $array, callable $where)
    {
        foreach ($array as $key => $val) {
            if ($where($key)) {
                return $val;
            }
        }
        return -1;
    }




    // RANDOM ----------------------------------------------------------------------------

    /**
     * Shuffle an array preserving the key/value pairs.
     *
     * @param array $array
     * @return array Returns a new array
     */
    public static function shuffleAssoc(array $array)
    {
        $keys = array_keys($array);
        shuffle($keys);
        $new = [];
        foreach ($keys as $key) {
            $new[$key] = $array[$key];
        }
        return $new;
    }

    /**
     * Selects random key/value pairs from the given array and returns
     * a new array with those pairs.
     *
     * @param array $array
     * @param int $count
     * @return array
     */
    public static function selectRand(array $array, $count = 1)
    {
        $keys = array_rand($array, $count);
        if (!is_array($keys)) {
            return $array[$keys];
        }
        return array_intersect_key($array, array_flip($keys));
    }




    // COMPUTING -----------------------------------------------------------------------------


    /**
     * Returns the sum of the array values
     *
     * @param array $array
     * @return int
     */
    public static function sum(array $array)
    {
        return array_reduce($array, function ($c, $v) {
            return $c += $v;
        }, 0);
    }

    /**
     * Returns the key with the highest value. If there are multiple keys
     * with the highest value, then the last one is returned.
     *
     * @param array $array
     * @return null|mixed
     */
    public static function maxKey(array $array)
    {
        if (empty($array)) {
            return null;
        }
        $max = max($array);
        $flipped = array_flip($array);
        return $flipped[$max];
    }

    /**
     * Returns the key with the lowest value. If there are multiple keys
     * with the lowest value, then the last one is returned.
     *
     * @param array $array
     * @return null|mixed
     */
    public static function minKey(array $array)
    {
        if (empty($array)) {
            return null;
        }
        $min = min($array);
        $flipped = array_flip($array);
        return $flipped[$min];
    }




    // COMPARING ---------------------------------------------------------------------------------------

    /**
     * Checks if two arrays have the same values for the given set of keys
     *
     * @param array $a
     * @param array $b
     * @param array $keys
     * @param bool $keysRequired If true, this fails if either array (a or b) does not contain a value for the provided keys
     * @return bool
     */
    public static function isSameByKeys(array $a, array $b, array $keys, $keysRequired = false)
    {
        if ($keysRequired && !(static::hasValuesForKeys($a, $keys) && static::hasValuesForKeys($b, $keys))) {
            return false;
        }
        return !count(array_diff_assoc(static::filterByKeys($a, $keys), static::filterByKeys($b, $keys)));
    }


    /**
     * Returns TRUE if the $target has a truey value for the given $keys.
     *
     * NOTE: Values with 0 are considered FALSE here.
     *
     * @param array $target
     * @param array $keys
     * @return bool
     */
    public static function hasValuesForKeys(array $target, array $keys)
    {
        $onlyBykeys = static::filterByKeys($target, $keys);
        $onlyByKeysCount = count($onlyBykeys);
        if ($onlyByKeysCount !== count($keys)) {
            return false;
        }
        array_walk($onlyBykeys, function (&$k) {
            $k = trim($k);
        });
        return $onlyByKeysCount == count(array_filter($onlyBykeys));
    }

    /**
     * Tells you how two arrays are different according to their keys.
     * Returns an array of the original key/values but split like so:
     *   'both'  -> Keys are the same in both arrays
     *   'alpha' -> Keys are unique to the alpha array
     *   'beta'  -> Keys are unique to the beta array
     *
     * @param array $alpha
     * @param array $beta
     * @return array
     */
    public static function describeDifferencesByKeys(array $alpha, array $beta)
    {
        return [
          'both' => array_intersect_key($alpha, $beta),
          'alpha' => array_diff_key($alpha, $beta),
          'beta' => array_diff_key($beta, $alpha),
        ];
    }

    /**
     * Tells you how two arrays are different.
     * Returns an array of the original values but split like so:
     *   'both'  -> Values are the same in both arrays
     *   'alpha' -> Values are unique to the alpha array
     *   'beta'  -> Values are unique to the beta array
     *
     * @param array $alpha
     * @param array $beta
     * @return array
     */
    public static function describeDifferences(array $alpha, array $beta)
    {
        return [
          'both' => array_intersect($alpha, $beta),
          'alpha' => array_diff($alpha, $beta),
          'beta' => array_diff($beta, $alpha),
        ];
    }




    // PROBING --------------------------------------------------------------------------------------------

    /**
     * Indexed if all keys are numeric (1 or "1")
     *
     * @param array $array
     * @return bool
     */
    public static function isIndexed(array $array)
    {
        foreach ($array as $key => $val) {
            if (!is_numeric($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Associative if no keys are numeric.
     *
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        foreach ($array as $key => $val) {
            if (is_numeric($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array|\Traversable $storage
     * @return bool
     */
    public static function isTraversable($storage)
    {
        return is_array($storage) || $storage instanceof \Traversable;
    }




    // CONVERTING -------------------------------------------------------------------------------------------

    /**
     * Creates a new array from a set of "rows" with the
     * provided arguments extracted out:
     *
     * $rows = [
     *   ['one' => 1, 'two' => 2, 'three' => 3],
     *   ['one' => 'a', 'two' => 'b', 'three' => 'c']
     * ];
     *
     * // You must pass a key or value field to use
     * Arr::column($rows); // throws error, must provide a key or a value column to use
     *
     * // Passing value and key fields returns associative array
     * Arr::column($rows, 'one', 'three') returns:  [3 => 1, 'c' => 'a']
     *
     * // Passing just value field returns indexed array
     * Arr::column($rows, 'one')          returns:  [1, 'a']
     *
     * // Passing just key field returns same array of "rows" with the key field as the index
     * Arr::column($rows, NULL, 'one')    returns:  [1 => ['one' => 1, ...], 'a' => [...]]
     *
     * @param array $rows
     * @param mixed $valueColumn
     * @param null $keyColumn
     * @throws \Exception
     * @return array
     */
    public static function column(array $rows, $valueColumn = null, $keyColumn = null)
    {
        if ($valueColumn === null && $keyColumn === null) {
            throw new \Exception("You must pass a key or value column to use on the given rows.");
        }
        $newRows = [];
        if ($keyColumn !== null && $valueColumn !== null) {
            foreach ($rows as $row) {
                $newRows[$row[$keyColumn]] = $row[$valueColumn];
            }
            return $newRows;
        }
        if ($valueColumn) {
            foreach ($rows as $row) {
                $newRows[] = $row[$valueColumn];
            }
            return $newRows;
        }
        if ($keyColumn) {
            foreach ($rows as $row) {
                $newRows[$row[$keyColumn]] = $row;
            }
            return $newRows;
        }
    }

    /**
     * Converts multidimensional associative arrays into a
     * stdClass object. This works recursively, but whenever
     * it comes across an indexed array (as opposed to
     * associative) it will leave that as an array though
     * it will convert any associative array values of that
     * indexed array into stdClass objects.
     *
     * @param array $array
     * @return \stdClass
     */
    public static function toStdClass(array $array)
    {
        return static::recursiveToStdClass($array);
    }

    /**
     * @param \stdClass $obj
     * @return array
     */
    public static function fromStdClass(\stdClass $obj)
    {
        return static::recursiveFromStdClass($obj);
    }

    /**
     * @param mixed $val
     * @return object
     */
    protected static function recursiveToStdClass($val)
    {
        if (is_array($val)) {
            if (!Arr::isAssoc($val)) {
                return $val;
            }
            return (object) array_map([static::class, 'recursiveToStdClass'], $val);
        } else {
            return $val;
        }
    }

    /**
     * @param mixed $val
     * @return array
     */
    protected static function recursiveFromStdClass($val)
    {
        if (is_object($val)) {
            $val = get_object_vars($val);
        }
        if (is_array($val)) {
            return array_map([static::class, 'recursiveFromStdClass'], $val);
        }
        return $val;
    }

    /**
     * Converts a comma list to an array. No-op for arrays. Throws exception
     * if not a string or an array.
     *
     * @param array|string $items
     * @param string $msg
     * @return array
     * @throws \Exception
     */
    public static function commaListToArray($items, $msg = "Expected comma list or array")
    {
        if (is_string($items)) {
            $items = array_map('trim', explode(',', $items));
        }
        if (!is_array($items)) {
            throw new \Exception($msg);
        }
        if (!count($items)) {
            return [];
        }
        return $items;
    }




    // ACCESS ------------------------------------------------------------------------------------------

    /**
     * Access an array element by index regardless of whether that
     * index has been defined.
     *
     * @param array|\ArrayAccess $array
     * @param mixed $index If $index is array, then it's assumed we should dig recursively
     * @param mixed $default
     * @return mixed
     */
    public static function safe($array, $index, $default = null)
    {
        if (!is_array($index)) {
            return isset($array[$index]) ? $array[$index] : $default;
        }
        // Peel a layer off the onion
        $key = array_shift($index);
        // Wherever we are, it's not good. so quit
        if (!isset($array[$key])) {
            return $default;
        }
        // If there's nowhere left to go, we're done.
        if (count($index) == 0) {
            return $array[$key];
        }
        // Dig deeper
        return static::safe($array[$key], $index, $default);
    }

    /**
     * Same as Arr::safe, but allows using a string value like "key.nestedKey.moreNestedKey"
     *
     * @param array $array
     * @param string $path
     * @param mixed $default
     * @param string $pathDelimiter
     * @return mixed
     * @throws \Exception
     */
    public static function safePath($array, $path, $default = null, $pathDelimiter = '.')
    {
        if (!is_string($path)) {
            throw new \Exception("Arr::safePath path must be a string");
        }
        $steps = explode($pathDelimiter, $path);
        return static::safe($array, $steps, $default);
    }

    /**
     * Sometimes you want to add elements to the end of an array,
     * but in nested arrays you don't know if the array you're
     * adding to has been initialized yet. This helps.
     *
     * @param array $array
     * @param mixed $index
     * @param null|mixed $value
     */
    public static function safeAppend(array &$array, $index, $value = null)
    {
        if (!is_array($array[$index])) {
            $array[$index] = [];
        }
        $array[$index][] = $value;
    }


}