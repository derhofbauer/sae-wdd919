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
     */
    public static function init ()
    {
        /**
         * Die session_start() Funktion erlaubt es, Config-Werte zu übergeben, unter anderem das Ablaufdatum des Session
         * Cookie.
         */
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

    /**
     * Diese Methode ermöglicht es uns auf die Werte, die in dem jeweils vorhergehenden Request in ein Formular
     * eingegeben wurden, zuzugreifen. Dadurch können wir Formularfelder mit Werten befüllen, wenn ein Fehler in der
     * Validierung auftritt, der/die User*in muss dann die Werte nicht nochmal eingeben, sondern lediglich korrigieren.
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public static function old (string $key, $default = null)
    {
        /**
         * Damit sowohl POST als auch GET Formular funktionieren, suchen wir in beiden Datenbeständen.
         */
        if (isset($_SESSION['$_post'][$key])) {
            /**
             * Wert aus der Session holen.
             */
            $_value = $_SESSION['$_post'][$key];
            /**
             * Wert in der Session löschen.
             */
            unset($_SESSION['$_post'][$key]);
            /**
             * Wert zurückgeben.
             */
            return $_value;
        }

        if (isset($_SESSION['$_get'][$key])) {
            $_value = $_SESSION['$_post'][$key];
            unset($_SESSION['$_post'][$key]);
            return $_value;
        }

        /**
         * Wird der übergeben $key nicht gefunden, so geben wir einen Standardwert zurück.
         */
        return $default;
    }
}
