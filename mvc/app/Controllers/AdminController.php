<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\User;
use Core\View;

/**
 * Class AdminController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class AdminController
{

    /**
     * @todo: comment
     */
    public function dashboard ()
    {
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * [ ] Übersicht über offene und neue Bestellungen
         * [ ] Übersicht über Produkte, von denen nicht mehr viele auf Lager sind
         */
        $products = Product::all('stock', 'ASC');

        View::render('admin/dashboard', [
            'products' => $products
        ]);
    }

}
