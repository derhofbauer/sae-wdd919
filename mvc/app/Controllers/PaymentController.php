<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Payment;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class PaymentController
 *
 * @package App\Controllers
 * @todo: comment
 */
class PaymentController
{

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function updateForm (int $id)
    {
        $payment = Payment::find($id);

        if (!User::isLoggedIn() || $payment->user_id !== User::getLoggedIn()->id) {
            View::error403();
        }

        View::render('profile-payment-form', [
            'payment' => $payment
        ]);
    }

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         *
         * @todo
         */
        if (!User::isLoggedIn()) {
            View::error403();
        }

        /**
         * Zu aktualisierende Zahlungsmethode aus der DB abfragen
         */
        $payment = Payment::find($id);

        /**
         * Prüfen, ob der/die eingeloggt User*in berechtigt ist, diese Adresse zu bearbeiten
         * @todo
         */
        if ($payment->user_id !== User::getLoggedIn()->id) {
            View::error403();
        }

        /**
         * Validator vorbereiten
         */
        $validator = new Validator();

        /**
         * Validierung
         */
        $validator->validate($_POST['name'], 'Name', true, 'text');
        $validator->validate($_POST['number'], 'Number', true, 'textnum');
        $validator->validate($_POST['ccv'], 'CCV', true, 'textnum');
        $validator->validate($_POST['expires'], 'expires', true, 'textnum');
        /**
         * Fehler aus Validator auslesen
         */
        $errors = $validator->getErrors();

        /**
         * Gibt es keine Fehler, legen wir ein neues Address Objekt an, übergeben die Werte und speichern das Objekt
         * in die Datenbank.
         *
         * @todo
         */
        if (empty($errors)) {

            $payment->name = $_POST['name'];
            $payment->number = $_POST['number'];
            $payment->ccv = $_POST['ccv'];
            $payment->expires = $_POST['expires'];

            $payment->save();

            Session::set('success', ['Zahlungsmethode erfolgreich aktualisiert']);
            header('Location: ' . BASE_URL . '/profile');
            exit;

        } else {
            /**
             * Gibt es Fehler speichern wir sie für die spätere Anzeige in die Session und leiten zurück zum Formular.
             */
            Session::set('errors', $errors);
            header('Location: ' . BASE_URL . '/profile/payments/' . $payment->id . '/edit');
            exit;
        }
    }
}
