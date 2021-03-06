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
        header('Location: ' . Session::get('referer'));
        exit;
    }

    /**
     * Inhalt aus dem Cart laden und an einen View zur Auflistung übergeben.
     */
    public function show ()
    {
        /**
         * Dammit wir den Inhalt des Carts auch an anderen Stellen einfach auslesen können, haben wir hier eine eigene
         * statische Methode dafür definiert. Die Syntax hier erlaubt es uns, wie in JavaScript, einen zurückgegebenen
         * Array zu destrukturieren. Dadurch sind im Prinzip mehrere Rückgabewerte aus einer Funktion möglich.
         */
        [$products, $total] = self::getCartContent();

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
             * Neue Quantity setzen oder Product löschen, wenn es auf 0 gesetzt wird
             */
            if ($newQuantity >= 1) {
                $cart[$productId] = $newQuantity;
            } else {
                unset($cart[$productId]);
            }
        }

        /**
         * Cart zurück in Session speichern
         */
        Session::set(self::CART_SESSION_KEY, $cart);

        /**
         * Zurück zum Cart leiten
         */
        header("Location: " . BASE_URL . '/cart');
        exit;
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
        exit;
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
        exit;
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
     * Inhalt des Carts auslesen und Gesamtpreis berechnen.
     *
     * Der Funktionsparameter kann die Berechnung der dynamisch hinzugefügten Eigenschaft "subtotal" unterbinden. Das
     * ist nützlich, wenn die Produkte für die Speicherung in einer Order serialisiert werden sollen und wir so wenig
     * wie möglich speichern möchten.
     *
     * @param bool $calculateSubTotal
     *
     * @return array
     */
    public static function getCartContent ($calculateSubTotal = true, $expandImages = false): array
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
             * Hier bieten wir die Möglichkeit über einen Funktionsparameter an, die Bilder direkt auch zu exploden und
             * somit zusätzlich auch als Array auszugeben. Das ist dann sinnvoll, wenn die Daten des Produkts in
             * irgendeiner Form als JSON ans JavaScript übergeben und dort weiterverwendet werden sollen.
             */
            if ($expandImages === true) {
                $product->_images = $product->getImages();
            }

            /**
             * $subtotal Property dynamisch in dem Produkt Objekt erstellen und berechnen, sofern das nicht aktiv
             * deaktiviert wurde durch den Funktionsparameter.
             */
            if ($calculateSubTotal === true) {
                $product->subtotal = $product->quantity * $product->price;

                /**
                 * Gesamten Warenwert des Warenkorbs erhöhen
                 */
                $total += $product->subtotal;
            }

            /**
             * "fertig" geladenes Produkt zu den übrigen geladenen Produkten pushen
             */
            $products[] = $product;
        }

        return [$products, $total];
    }

}
