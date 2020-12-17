<?php


namespace App\Controllers;

use Core\Session;
use Core\View;

/**
 * Class AjaxController
 *
 * @package App\Controllers
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
         * Benötigte Daten mit Array Destructuring aus der Methode holen.
         */
        [$cartContent, $total] = CartController::getCartContent(false, true);

        /**
         * Hier laden wir nicht wie bisher einen View, sondern geben einfach nur JSON zurück. Wir haben diese Helper
         * Methode geschrieben, damit wir eine sehr ähnliche Syntax haben wie mit View::render().
         */
        View::json([
            'numberOfProducts' => CartController::numberOfProducts(),
            'cartContent' => $cartContent
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
         * Benötigte Daten mit Array Destructuring aus der Methode holen.
         */
        [$cartContent, $total] = CartController::getCartContent(false, true);

        /**
         * Hier laden wir nicht wie bisher einen View, sondern geben einfach nur JSON zurück. Wir haben diese Helper
         * Methode geschrieben, damit wir eine sehr ähnliche Syntax haben wie mit View::render().
         */
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
         * Benötigte Daten mit Array Destructuring aus der Methode holen.
         */
        [$cartContent, $total] = CartController::getCartContent(true, true);

        /**
         * Hier laden wir nicht wie bisher einen View, sondern geben einfach nur JSON zurück. Wir haben diese Helper
         * Methode geschrieben, damit wir eine sehr ähnliche Syntax haben wie mit View::render().
         */
        View::json([
            'numberOfProducts' => CartController::numberOfProducts(),
            'cartContent' => $cartContent,
            'total' => $total
        ]);
    }

}
