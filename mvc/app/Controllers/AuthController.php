<?php

namespace App\Controllers;

use App\Models\User;
use Core\Session;
use Core\View;

/**
 * Class LoginController
 *
 * @package App\Controllers
 * @todo: comment
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
     *
     * @todo: comment
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

        $user = User::findByEmailOrUsername($_POST['usernameOrEmail']);

        $errors = [];

        if ($user === false || $user->checkPassword($_POST['password']) === false) {
            $errors[] = 'Username/E-Mail oder Passwort sind falsch';
        } else {
            $remember = false;
            if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                $remember = true;
            }

            if ($user->is_admin) {
                $user->login(BASE_URL . 'admin', $remember);
            } else {
                $user->login(BASE_URL . 'home', $remember);
            }
        }

        Session::set('errors', $errors);
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    /**
     * @todo: comment
     */
    public function logout ()
    {
        User::logout(BASE_URL . 'home');
    }

    /**
     * @todo: comment
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
