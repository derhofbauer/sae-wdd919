<?php

namespace App\Controllers;

use App\Models\Category;
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
         * Alle Produkte über das Product-Model aus der Datenbank laden.
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

    /**
     * @param int $id
     */
    public function category (int $id)
    {
        /**
         * Alle Produkte, die der Category $id zugewiesen sind, über das Product-Model aus der Datenbank laden.
         */
        $products = Product::findByCategoryId($id);

        /**
         * Um nicht in jeder Action den Header und den Footer und dann den View laden zu müssen, haben wir uns eine View
         * Klasse gebaut.
         */
        View::render('home', [
            'products' => $products
        ]);
    }
}
