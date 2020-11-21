<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\User;
use Core\Helpers\StaticData;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class AddressController
 *
 * @package App\Controllers
 */
class AddressController
{

    /**
     * Formular zur Änderung einer Adresse anzeigen.
     *
     * @param int $id
     */
    public function updateForm (int $id)
    {
        /**
         * [x] Adresse, die geändert werden solle, aus der DB laden
         * [x] Adresse in den Formlar-View übergeben
         */

        /**
         * Adresse aus der Datenbank laden.
         */
        $address = Address::find($id);

        /**
         * Prüfen ob ein*e User*in eingeloggt ist und Eigentümer*in der zu bearbeitenden Adresse ist.
         */
        if (!User::isLoggedIn() || $address->user_id !== User::getLoggedIn()->id) {
            /**
             * Wenn kein*s User*in eingeloggt ist ODER versucht wird, eine fremde Adresse zu bearbeiten, geben wir einen
             * Fehler 403 Forbidden zurück.
             */
            View::error403();
        }

        /**
         * Alle Länder der Welt aus der StaticData Helper Klasse abrufen, damit wir das Dropdown damit befüllen können.
         */
        $countries = StaticData::COUNTRIES;

        /**
         * View laden und Werte übergeben
         */
        View::render('profile-address-form', [
            'address' => $address,
            'countries' => $countries
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
         * Zu aktualisierende Adresse aus der DB abfragen
         */
        $address = Address::find($id);

        /**
         * Prüfen, ob der/die eingeloggt User*in berechtigt ist, diese Adresse zu bearbeiten
         */
        if ($address->user_id !== User::getLoggedIn()->id) {
            View::error403();
        }

        /**
         * Validator vorbereiten
         */
        $validator = new Validator();

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
         * Gibt es keine Fehler, legen wir ein neues Address Objekt an, übergeben die Werte und speichern das Objekt in
         * die Datenbank.
         */
        if (empty($errors)) {

            /**
             * Werte des zuvor geladenen Objekts im den Daten aus dem Formular überschreiben.
             */
            $address->country = $_POST['country'];
            $address->city = $_POST['city'];
            $address->zip = $_POST['zip'];
            $address->street = $_POST['street'];
            $address->street_nr = $_POST['street_nr'];
            $address->extra = $_POST['extra'];

            /**
             * Adress-Objekt in die Datenbank zurück speichern.
             */
            $address->save();

            /**
             * Erfolgsmeldung in die Session speichern und weiterleiten.
             */
            Session::set('success', ['Adresse erfolgreich aktualisiert']);
            header('Location: ' . BASE_URL . '/profile');
            exit;

        } else {
            /**
             * Gibt es Fehler speichern wir sie für die spätere Anzeige in die Session und leiten zurück zum Formular.
             */
            Session::set('errors', $errors);
            header('Location: ' . BASE_URL . '/profile/addresses/' . $address->id . '/edit');
            exit;
        }
    }

}
