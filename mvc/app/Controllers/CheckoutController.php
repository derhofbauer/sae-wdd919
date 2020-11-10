<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Payment;
use App\Models\User;
use Core\Helpers\StaticData;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class CheckoutController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class CheckoutController
{

    /**
     * Formular für eine neue Zahlungsmethode anzeigen
     *
     * @todo: comment
     */
    public function paymentForm ()
    {
        /**
         * [ ] Ist ein User eingeloggt?
         * [ ] Wenn ja: weiter, wenn nein: redirect zum Login
         * [ ] Payment Form anzeigen
         */
        $this->redirectIfNotLoggedIn();

        $userId = User::getLoggedIn()->id;
        $payments = Payment::findByUserId($userId);

        View::render('payment-form', [
            'payments' => $payments
        ]);
    }

    /**
     * @todo: comment (eine funktion macht normalerweise nur eine Sache)
     */
    public function handlePaymentForm ()
    {
        $this->redirectIfNotLoggedIn();

        $errors = [];
        $validator = new Validator();
        if ($_POST['submit-button'] === 'use-existing') {
            $validator->validate($_POST['card'], 'Card', true, 'int');
            $errors = $validator->getErrors();

            if (empty($errors)) {
                Session::set('checkout_payment', (int)$_POST['card']);
            }
        } elseif ($_POST['submit-button'] === 'create-new') {
            $validator->validate($_POST['name'], 'Name', true, 'text');
            $validator->validate($_POST['number'], 'Number', true, 'text');
            $validator->validate($_POST['ccv'], 'CCV', true, 'text');
            $validator->validate($_POST['expires'], 'expires', true, 'text');
            $errors = $validator->getErrors();

            if (empty($errors)) {
                $payment = new Payment();
                $payment->name = $_POST['name'];
                $payment->number = $_POST['number'];
                $payment->ccv = $_POST['ccv'];
                $payment->expires = $_POST['expires'];

                $payment->save();

                Session::set('checkout_payment', $payment->id);
            }
        } else {
            $errors[] = 'Bitte wählen Sie eines der Formulare aus.';
        }

        if (!empty($errors)) {
            Session::set('errors', $errors);
            header('Location: ' . BASE_URL . '/checkout');
            exit;
        }

        header('Location: ' . BASE_URL . '/checkout/address');
        exit;
    }

    /**
     * Formular für eine neue Lieferadresse anzeigen
     *
     * @todo: comment
     */
    public function addressForm ()
    {
        $this->redirectIfNotLoggedIn();

        $userId = User::getLoggedIn()->id;
        $addresses = Address::findByUserId($userId);
        $countries = StaticData::COUNTRIES;

        View::render('address-form', [
            'addresses' => $addresses,
            'countries' => $countries
        ]);
    }

    /**
     * Redirect to login, if no user is logged in
     *
     * @todo: comment
     */
    private function redirectIfNotLoggedIn ()
    {
        if (User::isLoggedIn() === false) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

}
