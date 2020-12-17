<?php

namespace App\Controllers;

use App\Models\Product;
use Core\Session;
use Core\View;

/**
 * Class SearchController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class SearchController
{

    /**
     * Handle search
     *
     * @todo: comment
     */
    public function search ()
    {
        $errors = [];
        $results = [];
        if (isset($_GET['searchterm']) && !empty($_GET['searchterm'])) {
            $searchterm = $_GET['searchterm'];

            $results = Product::search($searchterm);
        } else {
            $errors[] = 'Bitte geben Sie einen Suchbegriff ein.';
        }

        Session::set('errors', $errors);
        View::render('search', [
            'results' => $results
        ]);
    }

}
