<?php

namespace App\Controllers;

use Core\Database;
use Core\View;

/**
 * Class HomeController
 *
 * @package App\Controllers
 * @todo comment
 */
class HomeController
{

    public function home ()
    {
        $db = new Database();
        $products = $db->query('SELECT * FROM products');

        View::render('home', ['products' => $products]);
    }

}
