<?php

namespace Peach\Support;

class Path
{

    /**
     * 1. Get the extension
     * 2. Set the extension (if $newExt is there)
     *
     * @param string $path
     * @param string $newExt
     * @return string
     */
    public static function ext($path, $newExt = '')
    {
        if ($newExt) {
            return self::noExt($path) . '.' . ltrim($newExt, '.');
        }
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @param string $path
     * @return string mixed
     */
    public static function dir($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * @param string $path
     * @return string mixed
     */
    public static function noDir($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @param string $path
     * @return string mixed
     */
    public static function noExt($path)
    {
        $info = pathinfo($path);
        $dir = Arr::safe($info, 'dirname', '');
        if ($dir) {
            if ($dir == '.') {
                return $info['filename'];
            }
            return $dir . DIRECTORY_SEPARATOR . $info['filename'];
        }
        return $info['filename'];
    }

    /**
     * Path::make('some/dir','file','xml')
     * Path::make(['some','dir'], 'file', 'xml')
     * Path::make(['some','dir','file','xml'])
     * Path::make(['some','dir','file'], 'xml')
     * Path::make(['some','dir'], 'file.xml')
     * => 'some/dir/file.xml'
     *
     */
    public static function make()
    {
        $args = Arr::flatten(func_get_args());
        if (empty($args)) {
            return '';
        }
        $len = count($args);
        if ($len == 1) {
            return $args[0];
        }

        $fileAndExt = '';
        $ext = array_pop($args);
        if (strpos($ext, '.') !== false) {
            $fileAndExt = $ext;
        }

        if (!$fileAndExt) {
            $file = array_pop($args);
            $fileAndExt = $file . '.' . $ext;
        }

        $args[] = $fileAndExt;
        return static::join($args);
    }

    /**
     * Same as Path::make but no extension
     *
     * @return string
     */
    public static function join()
    {
        $args = Arr::flatten(func_get_args());
        if (empty($args)) {
            return '';
        }
        $len = count($args);
        if ($len == 1) {
            return $args[0];
        }
        $last = $len - 1;


        $args[0] = static::rtrim($args[0]);
        for ($i = 1; $i < $len; ++$i) {
            if ($i == $last) {
                $args[$i] = static::ltrim($args[$i]);
            } else {
                $args[$i] = static::trim($args[$i]);
            }

        }

        return implode(DIRECTORY_SEPARATOR, $args);
    }

    /**
     * Returns an array like [$commonPath, $path1Additional, $path2Additional]
     *
     * @param string $path1
     * @param string $path2
     * @return array
     */
    public static function diff($path1, $path2)
    {
        $p1 = explode(DIRECTORY_SEPARATOR, static::ext($path1) ? static::dir($path1) : $path1);
        $p2 = explode(DIRECTORY_SEPARATOR, static::ext($path2) ? static::dir($path2) : $path2);
        return array(
          implode(DIRECTORY_SEPARATOR, array_intersect_assoc($p1, $p2)),
          implode(DIRECTORY_SEPARATOR, array_diff_assoc($p1, $p2)),
          implode(DIRECTORY_SEPARATOR, array_diff_assoc($p2, $p1)),
        );
    }

    /**
     * @param $array
     * @return null|string
     */
    public static function common($array)
    {
        $out = static::dir(array_shift($array));
        foreach ($array as $path) {
            $diff = static::diff($out, static::dir($path));
            $out = $diff[0];
            if (!$out) {
                return null;
            }
        }
        return $out;
    }

    /**
     * Removes any slashes at the beginning or end
     *
     * @param string $path
     * @return string
     */
    protected static function trim($path)
    {
        return trim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Removes any slashes at the beginning or end
     *
     * @param string $path
     * @return string
     */
    protected static function rtrim($path)
    {
        return rtrim($path, DIRECTORY_SEPARATOR);
    }

    /**
     * Removes any slashes at the beginning or end
     *
     * @param string $path
     * @return string
     */
    protected static function ltrim($path)
    {
        return ltrim($path, DIRECTORY_SEPARATOR);
    }

}