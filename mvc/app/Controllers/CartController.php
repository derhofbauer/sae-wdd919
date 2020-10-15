<?php

namespace App\Controllers;

use Core\Session;

/**
 * Class CartController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class CartController
{

    const CART_SESSION_KEY = 'cart';

    /**
     * "productId" => "Anzahl im Warenkorb"
     *
     * @param int $id
     */
    public function add (int $id)
    {
        $cart = Session::get(self::CART_SESSION_KEY, []);

        if (array_key_exists((string)$id, $cart)) {
            $cart[$id] += $_POST['numberToAdd'];
        } else {
            $cart[$id] = $_POST['numberToAdd'];
        }

        Session::set(self::CART_SESSION_KEY, $cart);

        header("Location: " . Session::get('referrer'));
    }

    public function show ()
    {
        var_dump(Session::get(self::CART_SESSION_KEY));
    }

    public static function numberOfProducts ()
    {
        return array_sum(Session::get(self::CART_SESSION_KEY));
    }

}
