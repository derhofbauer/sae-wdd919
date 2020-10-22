<?php

namespace Core\Models;

use Core\Database;
use Core\Session;

/**
 * Class BaseUser
 *
 * @package Core\Models
 *               * @todo: comment
 */
abstract class BaseUser extends BaseModel
{
    /**
     *      * @todo: comment
     */
    const LOGGED_IN_STATUS = 'logged_in_status';
    const LOGGED_IN_ID = 'logged_in_id';
    const LOGGED_IN_REMEMBER = 'remember_until';
    const LOGGED_IN_SESSION_LIFETIME = 90 * 24 * 60 * 60; // 90 Tage in Sekunden

    /**
     * @param string $emailOrUsername
     *
     * @return false|mixed
     * @todo: comment
     */
    public static function findByEmailOrUsername (string $emailOrUsername)
    {
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
     * @param string $password
     *
     * @return bool
     * @todo: comment
     */
    public function checkPassword (string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @param string $redirect
     *
     * @param bool   $remember
     *
     * @return bool
     * @todo: comment
     */
    public function login (string $redirect = '', bool $remember = false): bool
    {
        Session::set(BaseUser::LOGGED_IN_STATUS, true);
        Session::set(BaseUser::LOGGED_IN_ID, $this->id);

        if ($remember === true) {
            Session::set(BaseUser::LOGGED_IN_REMEMBER, time() + BaseUser::LOGGED_IN_SESSION_LIFETIME);
        }

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
     * @todo: comment
     */
    public static function logout (string $redirect = ''): bool {
        Session::set(BaseUser::LOGGED_IN_STATUS, false);
        Session::forget(BaseUser::LOGGED_IN_ID);

        if (!empty($redirect)) {
            header("Location: $redirect");
            exit;
        }
        return true;
    }

    /**
     * @return bool
     * @todo: comment
     */
    public static function isLoggedIn (): bool
    {
        if (
            Session::get(BaseUser::LOGGED_IN_STATUS, false) === true
            || Session::get(BaseUser::LOGGED_IN_ID, null) !== null
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @todo: comment
     */
    public static function getLoggedIn ()
    {
        $userId = Session::get(BaseUser::LOGGED_IN_ID, null);

        if ($userId !== null) {
            $calledClass = get_called_class();
            return $calledClass::find($userId);
        }
        return false;
    }

}
