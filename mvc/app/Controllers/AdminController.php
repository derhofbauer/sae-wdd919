<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Core\View;

/**
 * Class AdminController
 *
 * @package App\Controllers
 */
class AdminController
{

    /**
     * Admin Dashboard ausgeben
     */
    public function dashboard ()
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * [ ] Übersicht über offene und neue Bestellungen
         * [ ] Übersicht über Produkte, von denen nicht mehr viele auf Lager sind
         */

        /**
         * Alle Produkte, von der Datenbank nach stock sortiert, auslesen
         */
        $products = Product::all('stock', 'ASC');

        /**
         * Alle User aus der Datenbank holen
         */
        $users = User::all('lastname', 'ASC');

        /**
         * Alle nicht abgeschlossenen und nicht stornierten Orders aus der Datenbank holen
         */
        $orders = Order::getOpenOrders();

        /**
         * Alle Categorien aus der Datenbank abfragen
         * @todo: comment
         */
        $categories = Category::all('name', 'ASC');
        foreach ($categories as $key => $category) {
            $productOfCategory = Product::findByCategoryId($category->id);
            $category->numberOfProducts = count($productOfCategory);
            $categories[$key] = $category;
        }

        /**
         * View laden und sortierte Produkte übergeben
         */
        View::render('admin/dashboard', [
            'products' => $products,
            'users' => $users,
            'orders' => $orders,
            'categories' => $categories
        ]);
    }

}
