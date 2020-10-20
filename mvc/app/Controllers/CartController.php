<?php

namespace App\Controllers;

use App\Models\Product;
use Core\Session;
use Core\View;

/**
 * Class CartController
 *
 * @package App\Controllers
 */
class CartController
{

    /**
     * Wir definieren uns hier eine Konstante, die wir dann später verwenden können. Das hat den Vorteil, dass der
     * tatsächlich verwendete Wert relativ egal ist, weil wir immer die Konstante verwenden, wenn wir auf
     * $_SESSION['cart'] zugreifen möchten.
     */
    const CART_SESSION_KEY = 'cart';

    /**
     * Nimmt eine ProductId entgegen und fügt ein Exemplar dieses Produkts in den Warenkorb hinzu.
     *
     * "productId" => "Anzahl im Warenkorb"
     *
     * @param int $id
     */
    public function add (int $id)
    {
        /**
         * Cart aus der Session laden
         */
        $cart = Session::get(self::CART_SESSION_KEY, []);

        /**
         * Prüfen, ob das Produkt, das hinzugefügt werden soll, schon im Cart ist
         */
        if (array_key_exists((string)$id, $cart)) {
            /**
             * Wenn ja, dann zählen wir die neue Anzahl hinzu
             */
            $cart[$id] += $_POST['numberToAdd'];
        } else {
            /**
             * Wenn nein, dann fügen wir das Produkt in der gewünschten Anzahl ins Cart ein
             */
            $cart[$id] = $_POST['numberToAdd'];
        }

        /**
         * Verändertes Cart zurück in die Session speichern
         */
        Session::set(self::CART_SESSION_KEY, $cart);

        /**
         * Redirect auf die Seite, von der wir gekommen sind.
         */
        header("Location: " . Session::get('referrer'));
    }

    /**
     * Inhalt aus dem Cart laden und an einen View zur Auflistung übergeben.
     *
     * @todo: comment
     */
    public function show ()
    {
        $cartContent = Session::get(self::CART_SESSION_KEY);

        $products = [];
        $total = 0;
        foreach ($cartContent as $productId => $quantity) {
            $product = Product::find($productId);
            $product->quantity = $quantity;
            $product->subtotal = $product->quantity * $product->price;
            $products[] = $product;
            $total += $product->subtotal;
        }

        View::render('cart', [
            'products' => $products,
            'total' => $total
        ]);
    }

    /**
     * @todo: comment
     */
    public function update ()
    {
        $cart = Session::get(self::CART_SESSION_KEY, []);

        foreach ($_POST['cart-quantity'] as $productId => $newQuantity) {
            $cart[$productId] = $newQuantity;
        }

        Session::set(self::CART_SESSION_KEY, $cart);

        header("Location: " . BASE_URL . 'cart');
    }

    /**
     * @todo: comment
     */
    public function addOne (int $id)
    {
        $cart = Session::get(self::CART_SESSION_KEY, []);

        if (array_key_exists($id, $cart)) {
            $cart[$id] += 1;
        } else {
            $cart[$id] = 1;
        }

        Session::set(self::CART_SESSION_KEY, $cart);

        header("Location: " . BASE_URL . 'cart');
    }

    /**
     * @todo: comment
     */
    public function removeOne (int $id)
    {
        $cart = Session::get(self::CART_SESSION_KEY, []);

        if (array_key_exists($id, $cart)) {
            $newQuantity = $cart[$id] - 1;
            if ($newQuantity >= 1) {
                $cart[$id] = $newQuantity;
            } else {
                unset($cart[$id]);
            }
        }

        Session::set(self::CART_SESSION_KEY, $cart);

        header("Location: " . BASE_URL . 'cart');
    }

    /**
     * Hier bieten wir eine statische Methode an, damit wir im Menü anzeigen können, wie viele Produkte im Warenkorb
     * sind.
     *
     * @return int
     */
    public static function numberOfProducts (): int
    {
        return array_sum(Session::get(self::CART_SESSION_KEY, []));
    }

}
