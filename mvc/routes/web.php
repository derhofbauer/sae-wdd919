<?php

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;

/**
 * Die Dateien im /routes Ordner beinhalten ein Mapping von einer URL auf eine eindeutige Controller & Action
 * kombination. Als Konvention definieren wir, dass URL-Parameter mit {xyz} definiert werden müssen, damit das Routing
 * korrekt funktioniert.
 */
return [
    /**
     * Home Routes
     */
    '/' => [HomeController::class, 'show'],
    '/home' => [HomeController::class, 'show'],
    '/products' => [HomeController::class, 'show'],

    /**
     * Product Routes
     */
    '/products/{id}' => [ProductController::class, 'show'],

    /**
     * Cart Routes
     */
    '/cart' => [CartController::class, 'show'],
    '/cart/add/{id}' => [CartController::class, 'add']
];
