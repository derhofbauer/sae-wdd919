<?php

namespace Core;

/**
 * Class Bootstrap
 *
 * @package Core
 */
class Bootstrap
{

    /**
     * [x] Session starten
     * [x] Routing laden
     */
    public function __construct ()
    {
        Session::init();

        $router = new Router();
        $router->route();
    }

    /**
     * Der Referrer ist idR. die letzte aufgerufene URL, vor der aktuellen URL, also mehr oder weniger der vorletzte
     * Request. Wir verwenden den Referrer dazu, dass wir nach einem Bearbeitungsformular beispielsweise zurück leiten
     * können und nicht an eine vordefinierte URL. Man könnte beispielsweise nach dem Login auf die Seite leiten, auf
     * der man davor war.
     * Damit der Referrer automatisch gesetzt wird, machen wir das im Destruktor der Bootstrap Klasse. Die letzte
     * ausgeführte Anweisung im Programmdurchlauf ist also diese Methode. Das bedeutet, dass bei jedem Programmdurchlauf
     * die vorhergehende URL über den Referrer abgerufen werden kann und erst wenn alles andere beendet ist, wird der
     * Referrer aktualisiert auf die aktuelle URL.
     */
    public function __destruct ()
    {
        /**
         * Angefragte URL zusammenbauen. S. https://stackoverflow.com/a/6768831
         */
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
        $currentUrl = "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        /**
         * Referrer in Session speichern
         */
        Session::set('referrer', $currentUrl);
    }

}
