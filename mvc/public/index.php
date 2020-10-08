<?php

/**
 * Bootstrap file
 *
 * Diese Datei ist der Startpunkt der gesamten Anwendung. Sie startet das Routing und ermöglicht dadurch das Laden von
 * Controllern.
 */

/**
 * spl_autoload_register Funktion akzeptiert einen Parameter, eine Funktion. Diese Funktion wird aufgerufen, wenn eine
 * Klasse verwendet werden soll, die noch nicht importiert wurde. Dieser Funktion wird der komplette Klassenname inkl.
 * Namespace übergeben.
 */
spl_autoload_register(function ($namespaceAndClassname) {
    /**
     * Hier versuchen wir den Namespace in einen validen Dateipfad umzuwandeln. Daher ist es wichtig, dass der
     * Klassenname und der Dateiname ident sind.
     *
     * z.B.:
     * + Core\Bootstrap => core/Bootstrap.php
     * + App\Models\User => app/Models/User.php
     */
    $namespaceAndClassname = str_replace('Core', 'core', $namespaceAndClassname);
    $namespaceAndClassname = str_replace('App', 'app', $namespaceAndClassname);
    $filepath = str_replace('\\', '/', $namespaceAndClassname);

    require_once __DIR__ . "/../{$filepath}.php";
});

/**
 * MVC "anstarten"
 */
$app = new \Core\Bootstrap();
