<?php

namespace App\Controllers;

use App\Models\Product;
use Core\View;

/**
 * Class ProductController
 *
 * @package App\Controllers
 */
class ProductController
{

    /**
     * @param int $id
     */
    public function show (int $id)
    {
        /**
         * Ein einzelnes Produkt anhand des Parameters in der URL über das Product Model aus der Datenbank abfragen.
         */
        $product = Product::find($id);

        /**
         * Produkt an View übergeben
         */
        View::render('product-single', [
            'product' => $product
        ]);
    }

}
