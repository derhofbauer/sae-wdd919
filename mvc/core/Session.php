<?php

namespace Core;

/**
 * Class Session
 *
 * Wir definieren deshalb eine eigene Klasse Session, damit wir überall, wo wir auf die Session zugreifen wollen diese
 * Wrapper-Klasse verwenden, damit die Session-Engine, also der Mechanismus, der die Daten dann speichert, ganz einfach
 * getauscht werden kann, ohne dass der ganze Code, der Sessions verwendet, umgebaut werden muss.
 *
 * @package Core
 */
class Session
{
    /**
     * Session starten
     *
     * @todo: comment
     */
    public static function init ()
    {
        session_start([
            'cookie_lifetime' => 90 * 24 * 60 * 60
        ]);
    }

    /**
     * Wert in Session schreiben
     *
     * @param string $key
     * @param        $value
     */
    public static function set (string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Wert aus Session auslesen
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function get (string $key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * Wert aus Session löschen
     *
     * @param string $key
     */
    public static function forget (string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Wert aus der Session auslesen und danach löschen
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function getAndForget (string $key, $default = null)
    {
        $_value = self::get($key, $default);
        self::forget($key);
        return $_value;
    }
}
