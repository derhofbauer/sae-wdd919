<?php


namespace App\Controllers;

use Core\Session;
use Core\View;

/**
 * Class AjaxController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class AjaxController
{

    /**
     * Anzahl eines Produkts in den Warenkorb hinzufügen
     *
     * Kommentare s. CartController
     *
     * @param int $id
     */
    public function addToCart (int $id)
    {
        $cart = Session::get(CartController::CART_SESSION_KEY, []);

        if (array_key_exists((string)$id, $cart)) {
            $cart[$id] += $_POST['numberToAdd'];
        } else {
            $cart[$id] = $_POST['numberToAdd'];
        }

        Session::set(CartController::CART_SESSION_KEY, $cart);

        /**
         * @todo: comment
         */
        View::json([
            'numberOfProducts' => CartController::numberOfProducts()
        ]);
    }

    /**
     * Ein Produkt ins Cart hinzufügen
     *
     * Kommentare s. CartController
     *
     * @param int $id
     */
    public function addOneToCart (int $id)
    {
        $cart = Session::get(CartController::CART_SESSION_KEY, []);

        if (array_key_exists($id, $cart)) {
            $cart[$id] += 1;
        } else {
            $cart[$id] = 1;
        }

        Session::set(CartController::CART_SESSION_KEY, $cart);

        /**
         * @todo:comment
         */
        [$cartContent, $total] = CartController::getCartContent();
        View::json([
            'numberOfProducts' => CartController::numberOfProducts(),
            'cartContent' => $cartContent,
            'total' => $total
        ]);
    }

    /**
     * Ein Produkt aus dem Cart entfernen
     *
     * Kommentare s. CartController
     *
     * @param int $id
     */
    public function removeOneFromCart (int $id)
    {
        $cart = Session::get(CartController::CART_SESSION_KEY, []);

        if (array_key_exists($id, $cart)) {
            $newQuantity = $cart[$id] - 1;

            if ($newQuantity >= 1) {
                $cart[$id] = $newQuantity;
            } else {
                unset($cart[$id]);
            }
        }

        Session::set(CartController::CART_SESSION_KEY, $cart);

        /**
         * @todo:comment
         */
        [$cartContent, $total] = CartController::getCartContent();
        View::json([
            'numberOfProducts' => CartController::numberOfProducts(),
            'cartContent' => $cartContent,
            'total' => $total
        ]);
    }

}
