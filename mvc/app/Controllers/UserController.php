<?php


namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class UserController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class UserController
{

    /**
     * User*innen Bearbeitungsformular anzeigen.
     *
     * @param int $id
     */
    public function updateForm (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * User*in, die/der bearbeitet werden soll, aus der Datenbank abfragen
         */
        $user = User::find($id);

        /**
         * User*in, die/der bearbeitet werden soll, an den View übergeben.
         */
        View::render('admin/user-update', [
            'user' => $user
        ]);
    }

    /**
     * User*in mit den Daten aus dem Bearbeitungsformular aktualisieren.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['firstname'], 'Firstname', true, 'text', 2, 255);
        $validator->validate($_POST['lastname'], 'Lastname', true, 'text', 2, 255);
        $validator->validate($_POST['username'], 'Username', false, 'textnum', null, 255);
        $validator->validate($_POST['email'], 'Email', true, 'email', 3, 255);

        /**
         * @todo: comment
         */
        if (isset($_POST['is_admin'])) {
            $validator->validate($_POST['is_admin'], 'Is Admin', false, 'checkbox');
        }

        if (!empty($_POST['password'])) {
            /**
             * Wir validieren das Passwort nur dann, wenn es gesetzt wurde.
             */
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
        }

        /**
         * Validierungsfehler aus dem Validator holen
         */
        $validationErrors = $validator->getErrors();

        /**
         * User*in, die/der bearbeitet werden soll, aus der Datenbank abfragen.
         *
         * Wir stellen die Abfrage hier rauf, damit wir prüfen können, ob die E-Mail Adresse oder der Username geändert
         * wurden.
         */
        $user = User::find($id);

        /**
         * @todo: comment
         */
        if ($user->email !== $_POST['email']) {
            if (User::findByEmailOrUsername($_POST['email']) !== false) {
                $validationErrors[] = 'Diese E-Mail-Adresse wird schon verwendet. Bitte wählen Sie eine andere';
            }
        }

        /**
         * @todo: comment
         */
        if ($user->username !== $_POST['username']) {
            if (User::findByEmailOrUsername($_POST['username']) !== false) {
                $validationErrors[] = 'Dieser Username wird schon verwendet. Bitte wählen Sie einen anderen';
            }
        }

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Bearbeitungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/users/' . $user->id . '/edit');
            exit;
        }

        /**
         * Eigenschaften des Datensatzes mit den Daten aus dem Formular aktualisieren.
         */
        $user->email = $_POST['email'];
        $user->username = $_POST['username'];
        $user->firstname = $_POST['firstname'];
        $user->lastname = $_POST['lastname'];

        /**
         * @todo: comment
         */
        if (isset($_POST['is_admin']) && $_POST['is_admin'] === 'on') {
            $user->is_admin = true;
        } else {
            if ($user->id !== User::getLoggedIn()->id) {
                $user->is_admin = false;
            }
        }

        /**
         * Wurde ein neues Passwort ins Formular eingegeben, dann setzen wir dem/der User*in einen neuen Hash
         */
        if (!empty($_POST['password'])) {
            $user->setPassword($_POST['password']);
        }

        /**
         * Geänderte/n User*in in der Datenbank aktualisieren.
         */
        $user->save();

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Dashboard.
         */
        Session::set('success', ['Der Account wurde erfolgreich aktualisiert.']);
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    /**
     * Ein/e User*in aus der Datenbank löschen.
     *
     * @param int $id
     */
    public function delete (int $id)
    {
        /**
         * User*in, der/die gelöscht werden soll, aus der Datenbank abfragen.
         */
        $user = User::find($id);

        /**
         * User*in löschen (Softdelete!)
         */
        $user->delete();

        /**
         * Redirect zum Dashboard.
         */
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

}
