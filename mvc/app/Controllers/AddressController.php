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
 * @todo    : comment
 */
class AddressController
{

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function updateForm (int $id)
    {
        /**
         * [x] Adresse, die geändert werden solle, aus der DB laden
         * [x] Adresse in den Formlar-View übergeben
         */


        $address = Address::find($id);

        if (!User::isLoggedIn() || $address->user_id !== User::getLoggedIn()->id) {
            View::error403();
        }

        /**
         * Alle Länder der Welt aus der StaticData Helper Klasse abrufen, damit wir das Dropdown damit befüllen können.
         */
        $countries = StaticData::COUNTRIES;

        View::render('profile-address-form', [
            'address' => $address,
            'countries' => $countries
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
         * Gibt es keine Fehler, legen wir ein neues Address Objekt an, übergeben die Werte und speichern das Objekt
         * in die Datenbank.
         *
         * @todo
         */
        if (empty($errors)) {

            $address->country = $_POST['country'];
            $address->city = $_POST['city'];
            $address->zip = $_POST['zip'];
            $address->street = $_POST['street'];
            $address->street_nr = $_POST['street_nr'];
            $address->extra = $_POST['extra'];

            $address->save();

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
