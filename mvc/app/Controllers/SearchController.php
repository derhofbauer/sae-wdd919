<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\Product;
use Core\Session;
use Core\View;

/**
 * Class SearchController
 *
 * @package App\Controllers
 */
class SearchController
{

    /**
     * Produkt Suche behandeln
     */
    public function search ()
    {
        /**
         * Fehler und Ergebnisse vorbereiten
         */
        $errors = [];
        $productResults = [];
        $blogResults = [];

        /**
         * Gibt es einen Suchbegriff ...
         */
        if (isset($_GET['searchterm']) && !empty($_GET['searchterm'])) {

            /**
             * ... so erstellen wir ein Alias ...
             */
            $searchterm = $_GET['searchterm'];

            /**
             * ... und setzen die Suche ab.
             */
            $productResults = Product::search($searchterm);
            $blogResults = Post::search($searchterm);

        } else {
            /**
             * Gibt es keinen Suchbegriff, schreiben wir einen Fehler.
             */
            $errors[] = 'Bitte geben Sie einen Suchbegriff ein.';
        }

        /**
         * Fehler in die Session speichern, damit sie später wieder angezeigt werden können.
         */
        Session::set('errors', $errors);

        /**
         * View laden und Daten übergeben.
         */
        View::render('search', [
            'productResults' => $productResults,
            'blogResults' => $blogResults
        ]);
    }

}
