<?php

namespace Core;

/**
 * Class View
 *
 * @package Core
 */
class View
{

    /**
     * Diese Methode erlaubt es uns innerhalb der Controller der App (s. HomeController), einen View in nur einer
     * einzigen Zeile zu laden und auch Parameter an den View zu übergeben. Die View Parameter dienen dazu, dass Werte,
     * die in den Controllern berechnet wurden, an den View zur Darstellung übergeben werden können.
     *
     * Aufruf: View::load('ProductSingle', $productValues)
     *
     * @param string $view
     * @param array  $params
     * @param string $layout
     */
    public static function render (string $view, array $params = [], string $layout = '')
    {
        /**
         * Standard-Layout laden, wenn kein $layout angegeben wurde.
         */
        if ($layout === '') {
            $layout = Config::get('app.default-layout');
        }

        /**
         * extract() erstellt aus jedem Wert in einem Array eine eigene Variable. Das brauchen wir aber nur zu tun, wenn
         * überhaupt $params vorhanden sind.
         */
        if (!empty($params)) {
            extract($params);
        }

        /**
         * View Base Path vorbereiten, damit ihn später an mehreren Stellen verwenden können.
         */
        $viewBasePath = __DIR__ . "/../resources/views";

        /**
         * View Path vorbereiten, damit im Layout file der View geladen werden kann
         */
        $viewPath = "$viewBasePath/templates/$view.php";

        /**
         * Hier laden wir das Layout-File anhand des $layout Funktionsparameters. Das Layout lädt dann den $view.
         */
        require_once "$viewBasePath/layouts/$layout.php";
    }

    /**
     * Das ist eine einfache Hilfsfunktion die den HTTP Status Code 404 an den Browser schickt und danach abbricht.
     */
    public static function error404 ()
    {
        header("HTTP/1.0 404 Not Found");
        die;
    }

    /**
     * Das ist eine einfache Hilfsfunktion die den HTTP Status Code 403 an den Browser schickt und danach abbricht.
     */
    public static function error403 ()
    {
        header("HTTP/1.0 403 Forbidden");
        die;
    }

}
