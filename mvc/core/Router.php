<?php

namespace Core;

/**
 * Class Router
 *
 * @package Core
 */
class Router
{

    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @var array
     */
    private array $paramNames = [];

    /**
     * Routen automatisiert laden
     */
    public function __construct ()
    {
        $this->loadRoutes();
    }

    /**
     * Routen laden
     *
     * Nachdem routes/web.php und roues/api.php beide einfach nur ein Array returnen, wird dieser Wert als Return-Wert
     * für das require_once verwendet und kann somit direkt in Variablen gespeichert werden.
     */
    public function loadRoutes ()
    {
        $webRoutes = require_once __DIR__ . '/../routes/web.php';
        $apiRoutes = require_once __DIR__ . '/../routes/api.php';
        /**
         * Property setzen, damit die Routen in diesem Objekt immer verfügbar sind
         */
        $this->routes = $webRoutes;

        /**
         * Alle API Routen durchgehen
         */
        foreach ($apiRoutes as $apiRoute => $controllerAndAction) {
            /**
             * Doppelte Slashes in allen API Routen entfernen und /api/ voranstellen
             */
            $apiRoute = str_replace('//', '/', "/api/$apiRoute");
            /**
             * "fertige" API Route zu den anderen Routes hinzufügen
             */
            $this->routes[$apiRoute] = $controllerAndAction;
        }
    }

    /**
     * $_GET['path'], die im .htaccess File definiert ist, verarbeiten und die richtige Controller/Action Kombination
     * aus den routes/*.php files suchen
     */
    public function route ()
    {
        /**
         * $_GET['path'] so umformen, dass immer ein führendes Slash dran steht und am Ende keines
         */
        $path = '';
        if (isset($_GET['path'])) {
            $path = $_GET['path'];
        }
        /**
         * `rtrim()` entfernt eine Liste an Zeichen vom Ende eines Strings.
         *
         * Wenn kein Pfad übergeben wurde, ist unsere Standarroute "/"
         */
        $path = '/' . rtrim($path, '/');

        /**
         * Variablen initialisieren, damit wir sie später befüllen können
         */
        $controller = '';
        $action = '';
        $params = [];

        /**
         * Prüfen, ob der angefragte Pfad als Route in unseren Routen 1:1 vorkommt oder nicht
         */
        if (array_key_exists($path, $this->routes)) {
            /**
             * Path existiert 1:1 so in unserem Routes Array, weil die Route keinen Parameter akzeptiert
             */

            /**
             * Abfragen der zugehörigen Controller Classe und Action Name zur angefragten Route
             */
            $controllerAndAction = $this->routes[$path];

            /**
             * Aufteilen und vordefinierte Variablen befüllen
             */
            $controller = $controllerAndAction[0];
            $action = $controllerAndAction[1];

        } else {

            /**
             * Wir müssen schauen, ob die Route möglicherweise einen Parameter beinhaltet
             *
             * Dazu gehen wir alle Routen durch.
             */
            foreach ($this->routes as $route => $controllerAndAction) {

                /**
                 * Wenn eine Route eine geschwungene Klammer beinhaltet, gibt es einen Parameter und wir formen sie in
                 * eine valide Regular Expression um. Wenn Sie keine geschwungene Klammer beinhaltet, dann wurde sie im
                 * oben stehenden `if` bereits abgedeckt und braucht nicht umgeformt zu werden.
                 */
                if (strpos($route, '{') !== false) {
                    /**
                     * Route in eine Regular Expression umformen
                     */
                    $regex = $this->prepareRegex($route);

                    /**
                     * Wird die einzelnen Treffer der Regular Expression beinhalten
                     *
                     * s. https://www.php.net/manual/en/function.preg-match-all.php
                     */
                    $matches = [];

                    /**
                     * Hier prüfen wir, ob der angefragte Pfad auf die Route im aktuellen Schleifendurchlauf zutrifft.
                     *
                     * preg_match_all() gibt bei einem Treffer 1 zurück.
                     */
                    if (preg_match_all($regex, $path, $matches, PREG_SET_ORDER) >= 1) {

                        /**
                         * $controllerAndACtion kommt diesmal aus der `foreach`-Schleife. Wir spalten es hier daher nur
                         * wieder genauso wie oben auf.
                         */
                        $controller = $controllerAndAction[0];
                        $action = $controllerAndAction[1];

                        /**
                         * Damit wir die Parameter mit Namen und Werten bekommen, gehen wir alle zuvor aus der Route
                         * extrahierten Parameter Namen durch und holen den zugehörigen Wert aus $matches.
                         */
                        foreach ($this->paramNames as $paramName) {
                            $params[$paramName] = $matches[0][$paramName];
                        }

                        /**
                         * Zu diesem Zeitpunkt wurde ein Treffer in den Routen gefunden und Controller, Action und
                         * Parameter aufgelöst. `break` beendet nun die aktuelle Schleife, weil wir nur einen Treffer
                         * brauchen und jede weitere Berechnung sinnlos ist.
                         *
                         *  s. https://www.php.net/manual/en/control-structures.break.php
                         */
                        break;
                    }
                }
            }
        }

        /**
         * Wenn kein Controller gefunden wurde, zeigen wir das an. Eigentlich sollten wir hier eine 404 Seite laden,
         * aber fürs erste reicht uns mal ein einfaches die().
         */
        if ($controller === '') {
            View::error404();
        } else {

            /**
             * Instanzieren (erzeugen) eines Controller Objects
             */
            $controller = new $controller(); // new \App\Controllers\BlogController();

            /**
             * Aufrufen der Methode $action aus dem Objekt $controller mit den Funktionsparametern $params.
             * Wir verwenden call_user_func_array, weil die Methode $action mit einer dynamischen Anzahl an Parametern
             * aufrufen müssen.
             *
             * s. https://www.php.net/manual/en/function.call-user-func-array.php
             */
            call_user_func_array([$controller, $action], $params); // $contoller->$action($params[0], $params[1], ...)
        }
    }

    /**
     * @param string $route
     *
     * @return string
     */
    public function prepareRegex (string $route): string
    {
        /**
         * Route mit Parameter in Regular Expression umformen:
         * - Slashes escapen (/ => \/)
         * - {param} mit einer Named Capture Group ersetzen
         * - Anfang und Ende des String setzen
         */
        $regex = str_replace('/', '\/', $route);

        /**
         * Um benannte Capture Groups erstellen zu können, müssen wir zunächst die Namen aller Parameter aus der Route
         * extrahieren. Das geht am einfachsten mit einer Regular Expression, die alles in der Form {xyz} sucht und den
         * Inhalt der Klammern in $matches schreibt.
         */
        $matches = [];
        preg_match_all('/{([a-zA-Z]+)}/', $regex, $matches);
        $this->paramNames = $matches[1];

        /**
         * Alle Parameter Namen durchgehen und innerhalb der $regex mit einer benannten Capture Group ersetzen, damit
         * am Ende eine valide Regular Expression raus kommt
         */
        foreach ($this->paramNames as $paramName) {
            $regex = str_replace("{{$paramName}}", "(?<$paramName>[^\/]+)", $regex);
        }

        /**
         * Anfang und Ende des Strings setzen und Regular Expression finalisieren
         */
        $regex = "/^$regex$/";

        /**
         * Fertige Regular Expression zurückgeben
         */
        return $regex;
    }


}
