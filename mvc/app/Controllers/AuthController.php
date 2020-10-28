<?php

namespace App\Controllers;

use App\Models\User;
use Core\Session;
use Core\View;

/**
 * Class LoginController
 *
 * @package App\Controllers
 */
class AuthController
{

    /**
     * Loin Formular anzeigen
     */
    public function loginForm ()
    {
        View::render('login');
    }

    /**
     * Login durchführen
     */
    public function doLogin ()
    {
        /**
         *      1) E-Mail/Username existiert in DB?
         *      2) Nein: Fehler, Ja: Weiter
         *      3) Password gegen Passwort Hash prüfen - Passwort stimmt?
         *      4) Nein: Fehler, Ja: Weiter
         *      5) Login-Status in Session speichern
         */

        /**
         * User anhand einer Email-Adresse oder eines Usernames aus der Datenbank laden
         */
        $user = User::findByEmailOrUsername($_POST['usernameOrEmail']);

        /**
         * Fehler-Array vorbereiten
         */
        $errors = [];

        /**
         * Wurde ein*e User*in in der Datenbank gefunden und stimmt das eingegebene Passwort mit dem Passwort Hash des/der User*in
         * überein?
         */
        if ($user === false || $user->checkPassword($_POST['password']) === false) {
            /**
             * Wenn nein: Fehler!
             */
            $errors[] = 'Username/E-Mail oder Passwort sind falsch';
        } else {
            /**
             * Wenn ja: weiter
             */

            /**
             * Remember Status vorbereiten
             */
            $remember = false;

            /**
             * Wenn die Rmember-Checkbox angehakerlt worden ist, ändern wir den Status
             */
            if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                $remember = true;
            }

            /**
             * Ist die/der User*in, der sich einloggen möchte ein Admin, so redirecten wir in den Admin-Bereich, sonst auf die
             * home-Seite.
             */
            if ($user->is_admin) {
                $user->login(BASE_URL . 'admin', $remember);
            } else {
                $user->login(BASE_URL . 'home', $remember);
            }
        }

        /**
         * Fehler in die Session schreiben und zum Login zurück leiten.
         */
        Session::set('errors', $errors);
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    /**
     * Logout und redirect auf die Home-Seite durchführen.
     */
    public function logout ()
    {
        User::logout(BASE_URL . 'home');
    }

    /**
     * Registrierungsformular anzeigen
     */
    public function signupForm ()
    {
        View::render('sign-up');
    }

    /**
     * @todo: comment
     */
    public function doSignup ()
    {
        /**
         * [ ] Validierung - Erfolgreich?
         * [ ] Ja: Weiter, Fehler: Fehler ausgeben
         * [ ] Gibts E-Mail oder Username schon in der DB?
         * [ ] Ja: Fehler ausgeben, Nein: weiter
         * [ ] User Objekt erstellen & in DB speichern
         * [ ] Weiterleitung zum Login Formular
         */
        var_dump($_POST);
    }

}
