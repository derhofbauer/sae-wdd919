<?php

use App\Controllers\HomeController;

/**
 * Die Dateien im /routes Ordner beinhalten ein Mapping von einer URL auf eine eindeutige Controller & Action
 * kombination. Als Konvention definieren wir, dass URL-Parameter mit {xyz} definiert werden müssen, damit das Routing
 * korrekt funktioniert.
 */
return [
    /**
     * Home Route
     */
    '/' => [HomeController::class, 'home'],
    '/home' => [HomeController::class, 'home'],
];
