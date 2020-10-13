<?php

namespace Core;

/**
 * Class Config
 *
 * @package Core
 */
class Config
{

    /**
     * Config auslesen
     *
     * Config::get('database.host') => config/database.php['host']
     *
     * @param string $key
     * @param null   $fallback
     * @todo: comment
     */
    public static function get (string $key, $fallback = null)
    {
        $fileAndName = explode('.', $key);
        $file = $fileAndName[0];
        $name = $fileAndName[1];
        $filename = __DIR__ . "/../config/$file.php";

        if (is_file($filename)) {
            $config = require $filename;

            if (array_key_exists($name, $config)) {
                return $config[$name];
            }
        }

        return $fallback;
    }

}
