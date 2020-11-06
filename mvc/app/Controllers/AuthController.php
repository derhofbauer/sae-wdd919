<?php

namespace App\Controllers;

use App\Models\User;
use Core\Session;
use Core\Validator;
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
                $user->login(BASE_URL . '/admin', $remember);
            } else {
                $user->login(BASE_URL . '/home', $remember);
            }
        }

        /**
         * Fehler in die Session schreiben und zum Login zurück leiten.
         */
        Session::set('errors', $errors);
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /**
     * Logout und redirect auf die Home-Seite durchführen.
     */
    public function logout ()
    {
        User::logout(BASE_URL . '/home');
    }

    /**
     * Registrierungsformular anzeigen
     */
    public function signupForm ()
    {
        View::render('sign-up');
    }

    /**
     * Daten aus dem Registrierungsformular entgegen nehmen und verarbeiten
     */
    public function doSignup ()
    {
        /**
         * [x] Validierung - Erfolgreich?
         * [x] Ja: Weiter, Fehler: Fehler ausgeben
         * [x] Gibts E-Mail oder Username schon in der DB?
         * [x] Ja: Fehler ausgeben, Nein: weiter
         * [x] User Objekt erstellen & in DB speichern
         * [x] Weiterleitung zum Login Formular
         */

        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['firstname'], 'Firstname', true, 'text', 2, 255);
        $validator->validate($_POST['lastname'], 'Lastname', true, 'text', 2, 255);
        $validator->validate($_POST['username'], 'Username', false, 'textnum', null, 255);
        $validator->validate($_POST['email'], 'Email', true, 'email', 3, 255);
        $validator->validate($_POST['password'], 'Password', true, 'password');
        /**
         * Das Feld 'password_repeat' braucht nicht validiert werden, weil wenn 'password' ein valides Passwort ist und
         * alle Kriterien erfüllt, und wir hier nun prüfen, ob 'password' und 'password_repeat' ident sind, dann ergibt
         * sich daraus, dass auch 'password_repeat' ein valides Passwort ist.
         */
        $validator->compare([
            $_POST['password'],
            'Passwort'
        ], [
            $_POST['password_repeat'],
            'Passwort wiederholen'
        ]);

        /**
         * Standardwert für die AGB-Checkbox setzen.
         */
        $agb = false;
        /**
         * Wenn die Checkbox aus dem Formular übergeben wurde, dann nehmen wir den Wert aus dem Formular, und verwenden
         * diesen weiter.
         */
        if (isset($_POST['agb'])) {
            $agb = $_POST['agb'];
        }
        /**
         * Validieren, ob die Checkbox einen validen Wert hat.
         */
        $validator->validate($agb, 'AGB', true, 'checkbox');

        /**
         * Fehler aus dem Validator auslesen.
         */
        $errors = $validator->getErrors();

        /**
         * Gibt es schon einen Account zur eingegebenen Email-Adresse?
         */
        if (User::findByEmailOrUsername($_POST['email']) !== false) {
            $errors[] = 'Diese E-Mail-Adresse ist bereits in Verwendung.';
        }

        /**
         * Wenn der Fehler-Array nicht leer ist und es somit Fehler gibt ...
         */
        if (!empty($errors)) {
            Session::set('errors', $errors);
            /**
             * ... dann speichern wir sie in die Session, damit sie im errors.php-Partial ausgegeben werden können und
             * leiten dann weiter.
             */
            header('Location: ' . BASE_URL . '/sign-up');
            exit;
        }

        /**
         * Kommen wir an diesen Punkt, können wir sicher sein, dass die E-Mail Adresse noch nicht verwendet wird und
         * alle eingegebenen Daten korrekt validiert werden konnten.
         */
        $user = new User();
        $user->email = $_POST['email'];
        $user->username = $_POST['username'];
        $user->firstname = $_POST['firstname'];
        $user->lastname = $_POST['lastname'];
        $user->setPassword($_POST['password']);

        /**
         * Neues Produkt in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($user->save()) {
            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zum Loginformular.
             *
             * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
             */
            Session::set('success', ['Der Account wurde erfolgreich angelegt. Sie können sich nun einloggen.']);

            /**
             * Redirect zum Login.
             */
            header('Location: ' . BASE_URL . '/login');
            exit;
        } else {
            /**
             * Fehlermeldung erstellung und in die Session speichern.
             */
            $validationErrors[] = 'Der Account konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Registrierungsformular.
             */
            header('Location: ' . BASE_URL . '/sign-up');
            exit;
        }
    }

}
