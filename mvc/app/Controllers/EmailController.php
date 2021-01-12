<?php

namespace App\Controllers;

use Core\Mail;
use Core\Session;
use Core\View;

/**
 * Class EmailController
 *
 * @package App\Controllers
 */
class EmailController
{

    /**
     * Kontaktformular anzeigen.
     */
    public function contactForm ()
    {
        View::render('contact');
    }

    /**
     * Daten aus dem Kontaktformular entgegennehmen und verarbeiten.
     */
    public function contact ()
    {
        /**
         * Hier müssten die Daten aus dem Formular validiert werden. Aus Gründen der Übersichtlichkeit habe ich aber
         * darauf verzichtet.
         */

        /**
         * Fehler Array vorbereiten und Variablen-Aliases erstellen.
         */
        $errors = [];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        /**
         * Neue E-Mail erstellen.
         */
        $mail = new Mail();
        $mail->setFrom($email, $name);
        $mail->addTo('contact@webshop.domain', 'Webshop Contact Form');
        $mail->subject = "Contact Form";
        $mail->message = $message;

        /**
         * E-Mail absenden und Rückgabewert prüfen.
         */
        if ($mail->send() === false) {
            /**
             * Tritt ein Fehler auf, holen wir uns diesen Fehler aus dem Mail-Objekt, generieren eine Fehlermeldung und
             * speichern die Fehler in die Session.
             */
            $errorMessage = $mail->error['message'];
            $errors[] = "Die E-Mail konnte nicht verschickt werden: $errorMessage";
            Session::set('errors', $errors);
        } else {
            /**
             * Im Erfolgsfall schreiben wir eine Erfolgsmeldung in die Session und löschen die Werte aus dem Formular,
             * die noch in der Session stehen.
             */
            Session::set('success', ['Das E-Mail wurde erfolgreich versendet.']);
            Session::forget('$_post');
            Session::forget('$_get');
        }

        /**
         * In jedem Fall leiten wir zurück zum Kontaktformular. Dort zeigen wir dann entweder eine Fehlermeldung oder
         * eine Erfolgsmeldung an.
         */
        header('Location: ' . BASE_URL . '/contact');
        exit;
    }
}
