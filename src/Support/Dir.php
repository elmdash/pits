<?php

namespace Peach\Support;

class Dir {

    /**
     * Returns an array of files and directories without "." or "..".
     *
     * @param string $path
     * @param bool $usePath Instead of just naming the files, return each file appended to the given $path
     * @return array
     */
    public static function listCommon($path, $usePath = FALSE)
    {
        if (!is_dir($path)) return FALSE;
        $res = array_slice(scandir($path), 2);
        if (!$usePath) return $res;
        return static::prependPath($path, $res);
    }

    /**
     * Returns an array of just directories without "." or "..".
     *
     * @param string $path
     * @param bool $usePath Instead of just naming the files, return each file appended to the given $path
     * @return array
     */
    public static function listDirs($path, $usePath = FALSE)
    {
        if (!is_dir($path)) return FALSE;
        $res = array_filter(array_slice(scandir($path), 2), function($f) use ($path) {
            return is_dir($path.DIRECTORY_SEPARATOR.$f);
        });
        if (!$usePath) return $res;
        return static::prependPath($path, $res);
    }

    /**
     * Returns an array of just the files (no directories).
     *
     * @param string $path
     * @param bool $usePath Instead of just naming the files, return each file appended to the given $path
     * @return array
     */
    public static function listFiles($path, $usePath = FALSE)
    {
        if (!is_dir($path)) return FALSE;
        $res = array_filter(array_slice(scandir($path), 2), function($f) use ($path) {
            return is_file($path.DIRECTORY_SEPARATOR.$f);
        });
        if (!$usePath) return $res;
        return static::prependPath($path, $res);
     }

    public static function dirsExist(array $dirs)
    {
        return Arr::findNot($dirs, 'is_dir') !== -1;
    }

    /**
     * Adds the path to all the array values (supposedly strings)
     *
     * @param string $path
     * @param $arr
     * @return array
     */
    protected static function prependPath($path, $arr)
    {
        if (!is_array($arr)) return $path.DIRECTORY_SEPARATOR.$arr;
        $prepend = function($f) use ($path) { return $path.DIRECTORY_SEPARATOR.$f; };
        return array_map($prepend, $arr);
    }
}