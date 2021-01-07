<?php

namespace App\Controllers;

use Core\Session;
use Core\View;

/**
 * @todo: comment
 */
class EmailController
{

    /**
     * @todo: comment
     */
    public function contactForm ()
    {
        View::render('contact');
    }

    /**
     * @todo: comment
     */
    public function contact ()
    {
        // @todo: Validierung verzichten
        $errors = [];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        $to = "$name <$email>";
        $subject = "Contact Form";
        $headers = [
            'From' => 'no-reply@localhost',
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        $result = mail($to, $subject, $message, $headers);

        if ($result === false) {
            $errorMessage = error_get_last()['message'];
            $errors[] = "Die E-Mail konnte nicht verschickt werden: $errorMessage";
            Session::set('errors', $errors);
        } else {
            Session::set('success', ['Das Produkt wurde erfolgreich gespeichert.']);
        }

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
         * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
         */
        header('Location: ' . BASE_URL . '/contact');
        exit;
    }
}
