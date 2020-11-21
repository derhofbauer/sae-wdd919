<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class PaymentController
 *
 * @package App\Controllers
 */
class PaymentController
{

    /**
     * Bearbeitungsformular für Zahlungsmethoden anzeigen.
     *
     * @param int $id
     */
    public function updateForm (int $id)
    {
        /**
         * Payment Method aus der Datenbank laden.
         */
        $payment = Payment::find($id);

        /**
         * Prüfen ob ein*e User*in eingeloggt ist und Eigentümer*in der zu bearbeitenden Zahlungsmethode ist.
         */
        if (!User::isLoggedIn() || $payment->user_id !== User::getLoggedIn()->id) {
            /**
             * Wenn kein*s User*in eingeloggt ist ODER versucht wird, eine fremde Zahlungsmethode zu bearbeiten, geben
             * wir einen Fehler 403 Forbidden zurück.
             */
            View::error403();
        }

        /**
         * View laden und Werte übergeben
         */
        View::render('profile-payment-form', [
            'payment' => $payment
        ]);
    }

    /**
     * Daten aus dem Adresse Bearbeitungsformular entgegen nehmen und verarbeiten.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein*e User*in eingeloggt ist. Wenn nicht, geben wir einen Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn()) {
            View::error403();
        }

        /**
         * Zu aktualisierende Zahlungsmethode aus der DB abfragen
         */
        $payment = Payment::find($id);

        /**
         * Prüfen, ob der/die eingeloggt User*in berechtigt ist, diese Zahlungsmethode zu bearbeiten
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
         * Gibt es keine Fehler, legen wir ein neues Address Objekt an, übergeben die Werte und speichern das Objekt in
         * die Datenbank.
         */
        if (empty($errors)) {

            /**
             * Werte des zuvor geladenen Objekts im den Daten aus dem Formular überschreiben.
             */
            $payment->name = $_POST['name'];
            $payment->number = $_POST['number'];
            $payment->ccv = $_POST['ccv'];
            $payment->expires = $_POST['expires'];

            /**
             * Payment-Objekt in die Datenbank zurück speichern.
             */
            $payment->save();

            /**
             * Erfolgsmeldung in die Session speichern und weiterleiten.
             */
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
