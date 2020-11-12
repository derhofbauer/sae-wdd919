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
        header("Location: " . Session::get('referer'));
    }

    /**
     * Inhalt aus dem Cart laden und an einen View zur Auflistung übergeben.
     */
    public function show ()
    {
        /**
         * @todo: comment
         */
        $productsAndTotal = self::getCartContent();
        $products = $productsAndTotal[0];
        $total = $productsAndTotal[1];

        /**
         * View laden und Werte übergebem
         */
        View::render('cart', [
            'products' => $products,
            'total' => $total
        ]);
    }

    /**
     * Genaue Anzahl eines Produktes in den Warenkorb legen
     */
    public function update ()
    {
        /**
         * Warenkorb aus der Session auslesen
         */
        $cart = Session::get(self::CART_SESSION_KEY, []);

        /**
         * Alle übergebenen Werte aus dem Cart-View durchgehen. Die Quantities haben deshalb alle den selben Namen,
         * 'cart-quantity', weil die name-Attribute der Input Felder Namen wie cart-quantity[1] und cart-quantity[42]
         * haben und die Werte dadurch als Array verfügbar sind.
         */
        foreach ($_POST['cart-quantity'] as $productId => $newQuantity) {
            /**
             * Neue Quantity setzen
             */
            $cart[$productId] = $newQuantity;
        }

        /**
         * Cart zurück in Session speichern
         */
        Session::set(self::CART_SESSION_KEY, $cart);

        /**
         * Zurück zum Cart leiten
         */
        header("Location: " . BASE_URL . '/cart');
    }

    /**
     * @param int $id
     */
    public function addOne (int $id)
    {
        /**
         * Cart aus Session auslesen
         */
        $cart = Session::get(self::CART_SESSION_KEY, []);

        /**
         * Existiert dieses Produkt bereits im Warenkorb?
         */
        if (array_key_exists($id, $cart)) {
            /**
             * Wenn ja, fügen wir ein Exemplar des Produkts hinzu
             */
            $cart[$id] += 1;
        } else {
            /**
             * Wenn nein, legen wir ein neues Exemplar dieses Produkts in den Warenkorb
             */
            $cart[$id] = 1;
        }

        /**
         * Warenkorb zurück in die Session speichern
         */
        Session::set(self::CART_SESSION_KEY, $cart);

        /**
         * Zurück zum Warenkorb View leiten
         */
        header("Location: " . BASE_URL . '/cart');
    }

    /**
     * @param int $id
     */
    public function removeOne (int $id)
    {
        /**
         * Warenkorb aus Session laden
         */
        $cart = Session::get(self::CART_SESSION_KEY, []);

        /**
         * Befindet sich das Produkt schon im Warenkorb?
         */
        if (array_key_exists($id, $cart)) {
            /**
             * Wenn ja, berechnen wir, wie oft es drin wäre, wenn wir eines davon weg nehmen.
             */
            $newQuantity = $cart[$id] - 1;
            /**
             *  Sind nach Abzug von einem Exemplar immernoch welche da, setzen wir diese neu berechnete veringerte Anzahl
             */
            if ($newQuantity >= 1) {
                $cart[$id] = $newQuantity;
            } else {
                /**
                 * Andernfalls löschn wir das Produkt aus dem Warenkorb.
                 *
                 * Die unset() Funktion kann verwendet werden um Variablen oder einzelne Einträge in Arrays zu löschen.
                 */
                unset($cart[$id]);
            }
        }

        /**
         * Warenkorb zurück in Session speichern
         */
        Session::set(self::CART_SESSION_KEY, $cart);

        /**
         * Zurück zum Cart-View leiten
         */
        header("Location: " . BASE_URL . '/cart');
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

    /**
     * @param bool $calculateSubTotal
     *
     * @return array
     * @todo: comment (incl. Props)
     */
    public static function getCartContent ($calculateSubTotal = true): array
    {
        /**
         * Cart aus der Session laden. Falls kein Cart in der Session gesetzt ist, nehmen wir hier ein leeres Array als
         * Standardwert.
         */
        $cart = Session::get(self::CART_SESSION_KEY, []);

        /**
         * Variablen vorbereiten; $total wird den Gesamtwert der Waren im Warenkorb beinhalten
         */
        $products = [];
        $total = 0;

        /**
         * Alle Einträge im Warenkorb durchgehen
         */
        foreach ($cart as $productId => $quantity) {
            /**
             * Zugehöriges Produkt aus der Datenbank laden
             */
            $product = Product::find($productId);

            /**
             * $quantity Property dynamisch in dem Produkt Objekt erstellen und mit der Wert aus der Session befüllen
             */
            $product->quantity = $quantity;

            /**
             * $subtotal Property dynamisch in dem Produkt Objekt erstellen und berechnen
             */
            if ($calculateSubTotal === true) {
                $product->subtotal = $product->quantity * $product->price;
            }

            /**
             * "fertig" geladenes Produkt zu den übrigen geladenen Produkten pushen
             */
            $products[] = $product;

            /**
             * Gesamten Warenwert des Warenkorbs erhöhen
             */
            $total += $product->subtotal;
        }

        return [$products, $total];
    }

}
