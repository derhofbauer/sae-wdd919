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
     * @param string $configString
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function get (string $configString, $default = null)
    {
        /**
         * Dateiname und Config-Key aus dem $configString auslesen
         */
        $fileAndName = explode('.', $configString);
        $file = $fileAndName[0];
        $name = $fileAndName[1];

        /**
         * Config filename generieren
         */
        $filename = __DIR__ . "/../config/$file.php";

        /**
         * Existiert die gewünschte Config-Datei?
         */
        if (is_file($filename)) {

            /**
             * Config file dynamisch laden
             */
            $config = require $filename;

            /**
             * Wenn der Config-Key in dem entsprechenden File existiert, dann geben wir den Wert davon zurück, sonst
             * geben wir den Wert von $default zurück.
             */
            if (array_key_exists($name, $config)) {
                return $config[$name];
            }
        }

        return $default;
    }

}
