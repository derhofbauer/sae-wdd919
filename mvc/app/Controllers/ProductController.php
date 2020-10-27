<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
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

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function updateForm (int $id)
    {
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        $product = Product::find($id);

        View::render('admin/product-update', [
            'product' => $product
        ]);
    }

    /**
     * @param int $id
     *
     * @todo: comment
     */
    public function update (int $id)
    {
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * HIER MÜSSTE VALIDIERT WERDEN!!
         */

        var_dump($_POST, $_FILES);

        /**
         * [x] Product abrufen
         * [x] Geänderte Formulardaten ins Product schreiben
         * [x] Bilder ggf. löschen
         * [ ] Datei-Upload verwalten
         * [x] Daten in die Datenbank speichern
         */
        $product = Product::find($id);
        $product->name = $_POST['name'];
        $product->description = $_POST['description'];
        $product->price = $_POST['price'];
        $product->stock = $_POST['stock'];

        if (isset($_POST['delete-image'])) {
            foreach ($_POST['delete-image'] as $path => $on) {
                $filename = basename($path);
                $product->removeImage($filename);
            }
        }


        $product->save();

        if (isset($_POST['delete-image'])) {
            foreach ($_POST['delete-image'] as $path => $on) {
                unlink(__DIR__ . "/../../$path");
            }
        }
    }

}
