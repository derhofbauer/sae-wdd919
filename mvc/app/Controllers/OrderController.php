<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Core\Session;
use Core\View;

/**
 * Class OrderController
 *
 * @package App\Controllers
 */
class OrderController
{

    const STATI = [
        'open' => 'Open',
        'in progress' => 'In progress',
        'in delivery' => 'In delivery',
        'storno' => 'Storno',
        'delivered' => 'Delivered! :D',
    ];

    /**
     * Product Bearbeitungsformular anzeigen.
     *
     * @param int $id
     */
    public function updateForm (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Order, die bearbeitet werden soll, aus der Datenbank abfragen
         */
        $order = Order::find($id);

        /**
         * Daten der Order, die über eine ID verknüpft sind, abrufen.
         */
        $address = Address::find($order->address_id);
        $payment = Payment::find($order->payment_id);
        $user = User::find($order->user_id);

        /**
         * Produkte aus der Order abrufen.
         */
        $products = $order->getProducts();

        /**
         * Subtotals & Gesamtpreis berechnen
         */
        $total = 0;
        foreach ($products as $key => $product) {
            $product->subtotal = $product->price * $product->quantity;
            $products[$key] = $product;
            $total += $product->subtotal;
        }

        /**
         * Order, die bearbeitet werden soll, an den View übergeben.
         */
        View::render('admin/order-update', [
            'order' => $order,
            'address' => $address,
            'payment' => $payment,
            'user' => $user,
            'products' => $products,
            'total' => $total
        ]);
    }

    /**
     * Daten aus dem Bearbeitungsformular entgegennehmen und verarbeiten.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Fehler-Array vorbereiten
         */
        $errors = [];

        /**
         * Erlaubte Stati aus der Klassenkonstante auslesen.
         *
         * Die array_keys()-Funktion gibt einen neuen Array zurück, der nur die Indizes des Input-Arrays als Werte
         * beinhaltet.
         */
        $allowedStati = array_keys(self::STATI);

        /**
         * Ist kein status übergeben worden und/oder kommt der status nicht im Array der erlaubten Stati vor ...
         */
        if (!isset($_POST['status']) || !in_array($_POST['status'], $allowedStati)) {
            /**
             * ... schreiben wir einen Fehler.
             */
            $errors[] = "Dieser Status ist nicht bekannt :(";

            /**
             * Wie immer speichern wir den Fehler für die spätere Anzeige in der Session und leiten zurück.
             */
            Session::set('errors', $errors);
            header('Location:' . BASE_URL . "/admin/orders/$id/edit");
            exit;
        }

        /**
         * Ist die Validierung nicht fehlgeschlagen, holen wir die Order aus der Datenbank, aktualisieren den Status
         * und speichern die geänderte Order zurück in die Datenbank.
         */
        $order = Order::find($id);
        $order->status = $_POST['status'];
        $order->save();

        /**
         * Danach schreiben wir eine Erfolgsmeldung in die Session und machen einen Redirect.
         */
        Session::set('success', ["Order #{$id} erfolgreich aktualisiert."]);
        header('Location:' . BASE_URL . "/admin");
        exit;
    }

}
