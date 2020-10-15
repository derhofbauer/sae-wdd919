<?php

namespace App\Controllers;

use App\Models\Product;
use Core\Database;
use Core\View;

/**
 * Class HomeController
 *
 * @package App\Controllers
 */
class HomeController
{

    public function show ()
    {
        /**
         * @todo: comment
         */
        $products = Product::all();

        /**
         * Um nicht in jeder Action den Header und den Footer und dann den View laden zu müssen, haben wir uns eine View
         * Klasse gebaut.
         */
        View::render('home', [
            'products' => $products
        ]);
    }

}
