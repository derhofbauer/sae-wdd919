<?php

namespace Core\Models;

use Core\Database;
use Core\Session;

/**
 * Class BaseUser
 *
 * @package Core\Models
 */
abstract class BaseUser extends BaseModel
{
    /**
     * Wir definieren zunächst ein paar Konstanten für die User Session, die wir später verwenden können.
     */
    const LOGGED_IN_STATUS = 'logged_in_status';
    const LOGGED_IN_ID = 'logged_in_id';
    const LOGGED_IN_REMEMBER = 'remember_until';
    const LOGGED_IN_SESSION_LIFETIME = 90 * 24 * 60 * 60; // 90 Tage in Sekunden

    /**
     * @param string $emailOrUsername
     *
     * @return false|mixed
     */
    public static function findByEmailOrUsername (string $emailOrUsername)
    {
        /**
         * Whitespace entfernen
         */
        $emailOrUsername = trim($emailOrUsername);

        /**
         * Datenbankverbindung herstellen.
         */
        $db = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tableName = self::getTableNameFromClassName();

        /**
         * Query ausführen.
         */
        $result = $db->query("SELECT * FROM $tableName WHERE email = ? OR username = ? LIMIT 1", [
            's:email' => $emailOrUsername,
            's:username' => $emailOrUsername
        ]);

        /**
         * Wurde ein Datensatz gefunden und gibt es somit Ergebnisse?
         */
        if (!empty($result)) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und zurückgeben.
             */
            $calledClass = get_called_class();
            return new $calledClass($result[0]);
        }

        /**
         * Es wurde kein Datensatz gefunden.
         */
        return false;
    }

    /**
     * Überprüfung, ob das übergebene $password auf den gespeicherten Hash zutrifft.
     *
     * Wir schreiben eine eigene Wrapper-Funktion, damit wir ohne Änderung an den Controllern einfach die Funktionsweise
     * der Passwort-Überprüfung ändern können.
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword (string $password): bool
    {
        /**
         * Die folgende Funktion kann einen plaintext Passwort gegen einen bcrypt Hash prüfen.
         */
        return password_verify($password, $this->password);
    }

    /**
     * Neues Passwort hashen und setzen.
     *
     * @param string $password
     */
    public function setPassword (string $password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $redirect
     *
     * @param bool   $remember
     *
     * @return bool
     */
    public function login (string $redirect = '', bool $remember = false): bool
    {
        /**
         * Login-Status in die Session speichern.
         */
        Session::set(BaseUser::LOGGED_IN_STATUS, true);
        Session::set(BaseUser::LOGGED_IN_ID, $this->id);

        /**
         * Wurde die Remember-Checkbox angehakerlt, definieren wir hier die Session Lifetime - wann der Login-Status
         * abläuft.
         */
        if ($remember === true) {
            Session::set(BaseUser::LOGGED_IN_REMEMBER, time() + BaseUser::LOGGED_IN_SESSION_LIFETIME);
        }

        /**
         * Wurde eine Redirect-URL übergeben, leiten wir hier weiter.
         */
        if (!empty($redirect)) {
            header("Location: $redirect");
            exit;
        }

        return true;
    }

    /**
     * @param string $redirect
     *
     * @return bool
     */
    public static function logout (string $redirect = ''): bool
    {
        /**
         * Login Status in der Session aktualisieren.
         */
        Session::set(BaseUser::LOGGED_IN_STATUS, false);
        Session::forget(BaseUser::LOGGED_IN_ID);
        Session::forget(BaseUser::LOGGED_IN_REMEMBER);

        /**
         * Wurde eine Redirect-URL übergeben, leiten wir hier weiter.
         */
        if (!empty($redirect)) {
            header("Location: $redirect");
            exit;
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function isLoggedIn (): bool
    {
        /**
         * Ist ein*e User*in eingeloggt, so geben wir true zurück ...
         */
        if (
            Session::get(BaseUser::LOGGED_IN_STATUS, false) === true
            && Session::get(BaseUser::LOGGED_IN_ID, null) !== null
        ) {
            return true;
        }

        /**
         * ... andernfalls false.
         */
        return false;
    }

    /**
     * @return bool
     */
    public static function getLoggedIn ()
    {
        /**
         * Ist ein*e User*in eingeloggt, so holen wir uns hier die zugehörige ID.
         */
        $userId = Session::get(BaseUser::LOGGED_IN_ID, null);

        /**
         * Wurde eine ID in der Session gefunden, laden wir den zugehörigen Datensatz aus der Datenbank und geben ihn
         * zurück.
         */
        if ($userId !== null) {
            /**
             * get_called_class() gibt den Namen der Klasse, die aufgerufen wurde, zurück. Nachdem unsere User Class
             * die BaseUser Class erweitert, können wir hier den Namen der User Class erhalten und die find Methode
             * dieser Klasse aufrufen.
             */
            $calledClass = get_called_class();
            return $calledClass::find($userId);
        }

        /**
         * Andernfalls haben wir nichts, was wir zurückgeben können und geben daher false zurück.
         */
        return false;
    }

}
