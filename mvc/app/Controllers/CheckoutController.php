<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Payment;
use App\Models\User;
use Core\Helpers\StaticData;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class CheckoutController
 *
 * @package App\Controllers
 */
class CheckoutController
{

    /**
     * Formular für eine neue Zahlungsmethode anzeigen
     */
    public function paymentForm ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und zum Login leiten, wenn nicht
         */
        $this->redirectIfNotLoggedIn();

        /**
         * Eingeloggte*n User*in abrufen
         */
        $userId = User::getLoggedIn()->id;
        /**
         * Zu einem früheren Zeitpunkt gespeicherte Zahlungsmethoden dieses Accounts abrufen
         */
        $payments = Payment::findByUserId($userId);

        /**
         * View laden und Zahlungsmethoden übergeben
         */
        View::render('payment-form', [
            'payments' => $payments
        ]);
    }

    /**
     * Zahlungsmethoden-Formular entgegennehmen und verarbeiten
     *
     * Normalerweise sollte eine Funktion nur eine Sache machen, die dafür aber gut. Hier brechen wir absichtlich mit
     * diesem Konzept, damit es einfacher verständlich ist, was diese Funktion macht.
     */
    public function handlePaymentForm ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und zum Login leiten, wenn nicht
         */
        $this->redirectIfNotLoggedIn();

        /**
         * Fehler Array vorbereiten
         */
        $errors = [];

        /**
         * Validator vorbereiten
         */
        $validator = new Validator();

        /**
         * Wenn das Formular zur Auswahl einer existierenden Zahlungsmethode abgeschickt wurde, erkennen wir das anhand
         * des value-Attributs des Submit-Buttons
         */
        if ($_POST['submit-button'] === 'use-existing') {
            /**
             * Validierung
             */
            $validator->validate($_POST['card'], 'Card', true, 'int');
            /**
             * Fehler aus Validator auslesen
             */
            $errors = $validator->getErrors();

            /**
             * Gibt es keine Fehler, speichern wir die ID der gewählten Zahlungsmethode in die Session um sie später
             * wieder verwenden zu können.
             */
            if (empty($errors)) {
                Session::set('checkout_payment', (int)$_POST['card']);
            }

        } elseif ($_POST['submit-button'] === 'create-new') {
            /**
             * Soll eine neue Zahlungsmethode angelegt werden?
             */

            /**
             * Validierung
             */
            $validator->validate($_POST['name'], 'Name', true, 'text');
            $validator->validate($_POST['number'], 'Number', true, 'text');
            $validator->validate($_POST['ccv'], 'CCV', true, 'text');
            $validator->validate($_POST['expires'], 'expires', true, 'text');
            /**
             * Fehler aus Validator auslesen
             */
            $errors = $validator->getErrors();

            /**
             * Gibt es keine Fehler, legen wir ein neues Payment Objekt an, übergeben die Werte und speichern das Objekt
             * in die Datenbank.
             */
            if (empty($errors)) {
                $payment = new Payment();
                $payment->name = $_POST['name'];
                $payment->number = $_POST['number'];
                $payment->ccv = $_POST['ccv'];
                $payment->expires = $_POST['expires'];

                $payment->save();

                /**
                 * Die Payment::save() Methode aktualisiert die neu generierte ID im zugehörigen Objekt. Wir können sie
                 * also, analog zum anderen Formular, in die Session speichern, damit wir sie später wieder verwenden
                 * können.
                 */
                Session::set('checkout_payment', $payment->id);
            }
        } else {
            /**
             * Wurde keines der beiden Formulare abgeschickt und diese Methode trotzdem aufgerufen, so schreiben wir
             * einen Fehler.
             */
            $errors[] = 'Bitte wählen Sie eines der Formulare aus.';
        }

        /**
         * Gibt es Fehler speichern wir sie für die spätere Anzeige in die Session und leiten zurück zu den Formularen.
         */
        if (!empty($errors)) {
            Session::set('errors', $errors);
            header('Location: ' . BASE_URL . '/checkout');
            exit;
        }

        /**
         * Gibt es keine Fehler, leiten wir weiter zum nächsten Schritt im Checkout.
         */
        header('Location: ' . BASE_URL . '/checkout/address');
        exit;
    }

    /**
     * Formular für eine neue Lieferadresse anzeigen
     */
    public function addressForm ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und zum Login leiten, wenn nicht
         */
        $this->redirectIfNotLoggedIn();

        /**
         * Eingeloggte*n User*in abrufen
         */
        $userId = User::getLoggedIn()->id;
        /**
         * Zu einem früheren Zeitpunkt gespeicherte Adressen dieses Accounts abrufen
         */
        $addresses = Address::findByUserId($userId);
        /**
         * Alle Länder der Welt aus der StaticData Helper Klasse abrufen, damit wir das Dropdown damit befüllen können.
         */
        $countries = StaticData::COUNTRIES;

        /**
         * View laden und Werte übergeben.
         */
        View::render('address-form', [
            'addresses' => $addresses,
            'countries' => $countries
        ]);
    }

    /**
     * Ist kein*e User*in eingeloggt, leitet diese Funktion zum Login.
     */
    private function redirectIfNotLoggedIn ()
    {
        if (User::isLoggedIn() === false) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

}
