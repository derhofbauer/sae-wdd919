<?php

namespace App\Controllers;

use Core\Session;

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
     */
    public function show ()
    {
        var_dump(Session::get(self::CART_SESSION_KEY));
    }

    /**
     * Hier bieten wir eine statische Methode an, damit wir im Menü anzeigen können, wie viele Produkte im Warenkorb
     * sind.
     *
     * @return int
     */
    public static function numberOfProducts (): int
    {
        return array_sum(Session::get(self::CART_SESSION_KEY));
    }

}
