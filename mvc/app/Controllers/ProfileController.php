<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class ProfileController
 *
 * @package App\Controllers
 */
class ProfileController
{

    /**
     * Profil Bearbeitungsformular anzeigen.
     */
    public function profileForm ()
    {
        /**
         * Eingeloggte*n User*in abfragen
         */
        $user = User::getLoggedIn();

        /**
         * Alle Adressen des/der User*in aus der DB abfragen
         */
        $addresses = Address::findByUserId($user->id);

        /**
         * Alle Payments des/der User*in aus der DB abfragen
         */
        $payments = Payment::findByUserId($user->id);

        /**
         * View laden und Account übergeben
         */
        View::render('profile-form', [
            'user' => $user,
            'addresses' => $addresses,
            'payments' => $payments
        ]);
    }

    /**
     * Aktuell eingeloggte*n User*in mit den Daten aus dem Profil-Formular aktualisieren.
     */
    public function profileUpdate ()
    {
        /**
         * [x] Aktuell eingeloggte*n User*in aus der Datenbank abfragen
         * [x] Daten aus dem Formular validieren
         * [x] Validierungsfehler handeln
         * [x] Formulardaten im/in der User*in aktualisieren
         * [x] User zurück in die DB speichern
         * [x] Redirect mit Erfolgsmeldung
         */

        /**
         * Eingeloggte*n User*in abfragen
         */
        $user = User::getLoggedIn();

        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['firstname'], 'Firstname', true, 'text', 2, 255);
        $validator->validate($_POST['lastname'], 'Lastname', true, 'text', 2, 255);
        $validator->validate($_POST['username'], 'Username', false, 'textnum', null, 255);
        $validator->validate($_POST['email'], 'Email', true, 'email', 3, 255);

        /**
         * Wenn ein Passwort in das Bearbeitungsformular eingegeben wurde, prüfen wir ob es alle Kriterien erfüllt.
         */
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
         * Wurde die E-Mail Adresse im Bearbeitungsformular verändert, prüfen wir, ob die neue E-Mail Adresse schon in
         * einem anderen Account verwendet wird.
         */
        if ($user->email !== $_POST['email']) {
            if (User::findByEmailOrUsername($_POST['email']) !== false) {
                $validationErrors[] = 'Diese E-Mail-Adresse wird schon verwendet. Bitte wählen Sie eine andere.';
            }
        }

        /**
         * Wurde der Username im Bearbeitungsformular verändert, prüfen wir, ob der neue Username schon in einem anderen
         * Account verwendet wird.
         */
        if ($user->username !== $_POST['username']) {
            if (User::findByEmailOrUsername($_POST['username']) !== false) {
                $validationErrors[] = 'Dieser Username wird schon verwendet. Bitte wählen Sie einen anderen.';
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
            header('Location: ' . BASE_URL . '/profile');
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
         * Wurde ein neues Passwort ins Formular eingegeben, dann setzen wir dem/der User*in einen neuen Hash
         */
        if (!empty($_POST['password'])) {
            $user->setPassword($_POST['password']);
        }

        /**
         * Geänderte*n User*in in der Datenbank aktualisieren.
         */
        $user->save();

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Profil-Formular.
         */
        Session::set('success', ['Ihr Profil wurde erfolgreich aktualisiert.']);
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    /**
     * Übersicht aller Bestellungen des/der aktuell eingeloggten User*in auflisten
     */
    public function orders ()
    {
        /**
         * [x] Aktuell eingeloggte*n User*in aus der Datenbank abfragen
         * [x] Zugehörige Orders aus der Datenbank holen
         * [x] Orders an View übergeben
         */

        /**
         * Eingeloggte*n User*in abfragen
         */
        $user = User::getLoggedIn();

        /**
         * Alle Orders zu dem Account aus der Datenbank abfragen
         */
        $orders = Order::findByUserId($user->id);

        /**
         * Gesamtpreis für alle abgefragten Orders berechnen. Hierzu brauchen wir zwei Schleifen, einmal um alle Orders
         * durchzugehen und darin eine, um alle Produkte einer Order durchzugehen und den Gesamtpreis zu berechnen.
         */
        foreach ($orders as $key => $order) {
            /**
             * Gesamtpreis berechnen
             */
            $total = 0;

            /**
             * Alle Produkte der Order durchgehen und die Subtotal zur Total dazu rechnen.
             */
            foreach ($order->getProducts() as $product) {
                $total += $product->price * $product->quantity;
            }

            /**
             * Neue Property $total in der Order erstellen
             */
            $order->total = $total;

            /**
             * Aktualisierte Order zurück in den Array speichern.
             */
            $orders[$key] = $order;
        }

        /**
         * View laden und Werte übergeben
         */
        View::render('orders', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

}
