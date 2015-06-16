<?php

namespace Peach\Support;

class Dir {

    /**
     * Returns an array of files and directories without "." or "..".
     *
     * @param string $path
     * @return array
     */
    public static function listCommon($path) {
        if (!is_dir($path)) return FALSE;
        return array_slice(scandir($path), 2);
    }

    /**
     * Returns an array of just directories without "." or "..".
     *
     * @param string $path
     * @return array
     */
    public static function listDirs($path) {
        if (!is_dir($path)) return FALSE;
        return array_filter(array_slice(scandir($path), 2), function($f) use ($path) {
            return is_dir($path.DIRECTORY_SEPARATOR.$f);
        });
    }

    /**
     * Returns an array of just the files (no directories).
     *
     * @param string $path
     * @return array
     */
    public static function listFiles($path) {
        if (!is_dir($path)) return FALSE;
        return array_filter(array_slice(scandir($path), 2), function($f) use ($path) {
            return is_file($path.DIRECTORY_SEPARATOR.$f);
        });
    }
}