<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Order;
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
                $payment->user_id = User::getLoggedIn()->id;

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
     * Daten aus dem Adress Formular entgegennehmen und verarbeiten.
     */
    public function handleAddressForm ()
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
            $validator->validate($_POST['address'], 'Address', true, 'int');
            /**
             * Fehler aus Validator auslesen
             */
            $errors = $validator->getErrors();

            /**
             * Gibt es keine Fehler, speichern wir die ID der gewählten Zahlungsmethode in die Session um sie später
             * wieder verwenden zu können.
             */
            if (empty($errors)) {
                Session::set('checkout_address', (int)$_POST['address']);
            }

        } elseif ($_POST['submit-button'] === 'create-new') {
            /**
             * Soll eine neue Adresse angelegt werden?
             */

            /**
             * Validierung
             */
            $validator->validate($_POST['country'], 'Country', true, 'textnum');
            $validator->validate($_POST['city'], 'City', true, 'textnum');
            $validator->validate($_POST['zip'], 'ZIP', true, 'textnum');
            $validator->validate($_POST['street'], 'street', true, 'textnum');
            $validator->validate($_POST['street_nr'], 'Street Number', true, 'textnum');
            $validator->validate($_POST['extra'], 'Additional Line', false, 'textnum');
            /**
             * Fehler aus Validator auslesen
             */
            $errors = $validator->getErrors();

            /**
             * Gibt es keine Fehler, legen wir ein neues Address Objekt an, übergeben die Werte und speichern das Objekt
             * in die Datenbank.
             */
            if (empty($errors)) {
                $address = new Address();
                $address->country = $_POST['country'];
                $address->city = $_POST['city'];
                $address->zip = $_POST['zip'];
                $address->street = $_POST['street'];
                $address->street_nr = $_POST['street_nr'];
                $address->extra = $_POST['extra'];
                $address->user_id = User::getLoggedIn()->id;

                $address->save();

                /**
                 * Die Payment::save() Methode aktualisiert die neu generierte ID im zugehörigen Objekt. Wir können sie
                 * also, analog zum anderen Formular, in die Session speichern, damit wir sie später wieder verwenden
                 * können.
                 */
                Session::set('checkout_address', $address->id);
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
            header('Location: ' . BASE_URL . '/checkout/address');
            exit;
        }

        /**
         * Gibt es keine Fehler, leiten wir weiter zum nächsten Schritt im Checkout.
         */
        header('Location: ' . BASE_URL . '/checkout/final');
        exit;
    }

    /**
     * Finale Anzeige der zu bestellenden Produkte und von gewählter Lieferadresse und Zahlungsmethode.
     */
    public function finalForm ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und zum Login leiten, wenn nicht
         */
        $this->redirectIfNotLoggedIn();

        /**
         * IDs von gewählter Lieferadresse und Zahlungsmethode aus der Session holen.
         */
        $payment_id = Session::get('checkout_payment', false);
        $address_id = Session::get('checkout_address', false);

        /**
         * Fehler-Array vorbereiten
         */
        $errors = [];

        /**
         * Wenn sowohl eine payment_id als auch eine address_id in der Session gefunden wurden ...
         */
        if ($payment_id && $address_id) {

            /**
             * ... laden wir beide Datensätze aus der Datenbank.
             */
            $payment = Payment::find($payment_id);
            $address = Address::find($address_id);

            /**
             * Inhalt des Carts und Gesamtpreis laden.
             */
            [$products, $total] = CartController::getCartContent();

            /**
             * Alle Daten in den View übergeben.
             */
            View::render('checkout-final', [
                'payment' => $payment,
                'address' => $address,
                'products' => $products,
                'total' => $total
            ]);

        } else {
            /**
             * Fehler schreiben, wenn Zahlungsmethode und/oder Adresse nicht aus der Session abgerufen werden konnten.
             */
            $errors[] = 'Adresse oder Zahlungsinformationen konnten nicht geladen werden.';

            /**
             * Fehler in die Session speichern, damit wir sie nach dem Redirect ausgeben können.
             */
            Session::set('errors', $errors);
            header('Location: ' . BASE_URL . '/checkout/address');
            exit;
        }
    }

    /**
     * Nach der finalen Kontrollübersicht eine Bestellung aus den Daten in der Session erstellen.
     */
    public function finish ()
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist und zum Login leiten, wenn nicht
         */
        $this->redirectIfNotLoggedIn();

        /**
         * IDs von gewählter Lieferadresse und Zahlungsmethode aus der Session holen.
         */
        $payment_id = Session::get('checkout_payment', false);
        $address_id = Session::get('checkout_address', false);

        /**
         * Fehler-Array vorbereiten
         */
        $errors = [];

        /**
         * Wenn sowohl eine payment_id als auch eine address_id in der Session gefunden wurden ...
         */
        if ($payment_id && $address_id) {

            /**
             * ... laden wir beide Datensätze aus der Datenbank.
             */
            $productsAndTotal = CartController::getCartContent(false);
            $products = $productsAndTotal[0];

            /**
             * Neues Order-Objekt anlegen und Werte aus der Session übergeben.
             */
            $order = new Order();
            $order->user_id = User::getLoggedIn()->id;
            $order->payment_id = $payment_id;
            $order->address_id = $address_id;
            $order->products = $products;
            /**
             * Order-Objekt in die Datenbank speichern.
             */
            $order->save();

            /**
             * Cart leeren und IDs, die für die Order verwendet wurden, aus der Session löschen.
             */
            Session::forget(CartController::CART_SESSION_KEY);
            Session::forget('checkout_payment');
            Session::forget('checkout_address');

            /**
             * Erfolgsmeldung in die Session speichern.
             */
            Session::set('success', ["Bestellug #{$order->id} wurde erfolgreich gespeichert!"]);

            /**
             * Redirect.
             */
            header('Location: ' . BASE_URL . '/home');
            exit;
        } else {
            /**
             * Fehler schreiben, wenn Zahlungsmethode und/oder Adresse nicht aus der Session abgerufen werden konnten.
             */
            $errors[] = 'Adresse oder Zahlungsinformationen konnten nicht geladen werden.';

            /**
             * Fehler in die Session speichern, damit wir sie nach dem Redirect ausgeben können.
             */
            Session::set('errors', $errors);
            header('Location: ' . BASE_URL . '/checkout/final');
            exit;
        }

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
