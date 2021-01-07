<?php

namespace App\Controllers;

use Core\Mail;
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

        $mail = new Mail();
        $mail->setFrom($name, $email);
        $mail->addTo('Webshop Contact Form', 'contact@webshop.domain');
        $mail->subject = "Contact Form";
        $mail->message = $message;

        if ($mail->send() === false) {
            $errorMessage = $mail->error['message'];
            $errors[] = "Die E-Mail konnte nicht verschickt werden: $errorMessage";
            Session::set('errors', $errors);
        } else {
            Session::set('success', ['Das E-Mail wurde erfolgreich versendet.']);
            Session::forget('$_post');
            Session::forget('$_get');
        }

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
         * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
         */
        header('Location: ' . BASE_URL . '/contact');
        exit;
    }
}
