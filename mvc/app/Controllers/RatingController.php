<?php

namespace App\Controllers;

use App\Models\Rating;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * @todo: comment
 */
class RatingController
{

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function create (int $id)
    {
        if (!User::isLoggedIn()) {
            View::error403();
        }

        /**
         * [x] Hat der aktuelle User das Produkt schon einmal bewertet?
         * [x] Rating Daten validieren
         * [x] Rating Objekt erstellen
         * [x] Rating Objekt speichern
         * [x] zurück zum Produkt leiten
         */

        $errors = [];

        $existingRating = Rating::findByUserIdAndProductId(User::getLoggedIn()->id, $id);
        if (!empty($existingRating)) {
            $errors[] = 'Ein Produkt kann nur einmal bewertet werden.';
        }

        $validator = new Validator();
        if (isset($_POST['rating'])) {
            $validator->validate($_POST['rating'], 'Rating', true, 'int');
        } else {
            $errors[] = "Rating ist ein Pflichtfeld.";
        }
        $validator->validate($_POST['comment'], 'Comment', false, 'textnum');

        $errors = array_merge($errors, $validator->getErrors());

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $errors);
        } else {
            $rating = new Rating();
            $rating->user_id = User::getLoggedIn()->id;
            $rating->product_id = (int)$id;
            $rating->rating = (int)$_POST['rating'];
            $rating->comment = $_POST['comment'];
            $rating->save();

            Session::set('success', ['Das Rating wurde erfolgreich gespeichert.']);
        }

        /**
         * Redirect zurück zum Bearbeitungsformular.
         */
        header('Location: ' . BASE_URL . "/products/$id");
        exit;
    }

}
