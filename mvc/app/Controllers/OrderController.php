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
 * @todo    : comment
 */
class OrderController
{

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
         * @todo: comment
         */
        $address = Address::find($order->address_id);
        $payment = Payment::find($order->payment_id);
        $user = User::find($order->user_id);

        /**
         * @todo: comment
         */
        $products = $order->getProducts();
        $total = 0;
        foreach ($products as $key => $product) {
            $product->subtotal = $product->price * $product->quantity;
            $products[$key] = $product;
            $total += $product->subtotal;
        }

        /**
         * Order, die bearbeitet werden soll, an den View übergeben.
         *
         * @todo: comment
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
     * @param int $id
     *
     * @todo: comment
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

        $errors = [];
        $allowedStati = ['open', 'in progress', 'in delivery', 'storno', 'delivered'];
        if (!isset($_POST['status']) || !in_array($_POST['status'], $allowedStati)) {
            $errors[] = "Dieser Status ist nicht bekannt :(";

            Session::set('errors', $errors);
            header('Location:' . BASE_URL . "/admin/orders/$id/edit");
            exit;
        }

        $order = Order::find($id);
        $order->status = $_POST['status'];
        $order->save();

        Session::set('success', ["Order #{$id} erfolgreich aktualisiert."]);
        header('Location:' . BASE_URL . "/admin");
        exit;
    }

}
