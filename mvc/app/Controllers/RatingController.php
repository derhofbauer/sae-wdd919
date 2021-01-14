<?php

namespace App\Controllers;

use App\Models\Rating;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class RatingController
 *
 * @package App\Controllers
 */
class RatingController
{

    /**
     * Ein neues Rating Objekt speichern.
     *
     * @param int $id
     */
    public function create (int $id)
    {
        /**
         * Nur eingeloggt Accounts dürfen Ratings abgeben.
         */
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

        /**
         * Fehler Array vorbereiten
         */
        $errors = [];

        /**
         * Alle Ratings für die Kombination aus aktuellem Produkt und Account aus der Datenbank laden.
         */
        $existingRating = Rating::findByUserIdAndProductId(User::getLoggedIn()->id, $id);
        /**
         * Wenn Ratings gefunden wurden bedeutet das, dass dieser aktuelle User das Produkt bereits geratet hat.
         */
        if (!empty($existingRating)) {
            /**
             * In diesem Fall schreiben wir einen Fehler.
             */
            $errors[] = 'Ein Produkt kann nur einmal bewertet werden.';
        }

        /**
         * Daten validieren
         */
        $validator = new Validator();
        /**
         * Rating darf nur dann validiert werden, wenn es gesetzt ist, weil sonst werden wir einen Fehler kriegen.
         */
        if (isset($_POST['rating'])) {
            $validator->validate($_POST['rating'], 'Rating', true, 'int');
        } else {
            $errors[] = "Rating ist ein Pflichtfeld.";
        }
        $validator->validate($_POST['comment'], 'Comment', false, 'textnum');

        /**
         * Bereits bestehende Fehler und Validierungsfehler zusammenführen.
         */
        $errors = array_merge($errors, $validator->getErrors());

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($errors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten später zurück zum Bearbeitungsformular, wo die
             * Fehler über das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $errors);
        } else {
            /**
             * Sind keine Fehler aufgetreten, erstellen wir ein neues Rating Objekt und speichern es in die Datenbank.
             */
            $rating = new Rating();
            $rating->user_id = User::getLoggedIn()->id;
            $rating->product_id = (int)$id;
            $rating->rating = (int)$_POST['rating'];
            $rating->comment = $_POST['comment'];
            $rating->save();

            /**
             * Dann schreiben wir eine Erfolgsmeldung und leiten später weiter.
             */
            Session::set('success', ['Das Rating wurde erfolgreich gespeichert.']);
        }

        /**
         * Redirect zurück zum Produkt.
         */
        header('Location: ' . BASE_URL . "/products/$id");
        exit;
    }

}
