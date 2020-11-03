<?php

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;

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
    '/cart/update' => [CartController::class, 'update'],
    '/cart/add/{id}' => [CartController::class, 'add'],
    '/cart/add-one/{id}' => [CartController::class, 'addOne'],
    '/cart/remove-one/{id}' => [CartController::class, 'removeOne'],

    /**
     * Login Routes
     */
    '/login' => [AuthController::class, 'loginForm'],
    '/login/do' => [AuthController::class, 'doLogin'],
    '/logout' => [AuthController::class, 'logout'],

    /**
     * Sign-up Routes
     */
    '/sign-up' => [AuthController::class, 'signupForm'],
    '/sign-up/do' => [AuthController::class, 'doSignup'],

    /**
     * Admin Route
     */
    '/admin' => [AdminController::class, 'dashboard'],
    '/admin/products/{id}/edit' => [ProductController::class, 'updateForm'],
    '/admin/products/{id}/edit/do' => [ProductController::class, 'update'],
    '/admin/products/create' => [ProductController::class, 'createForm'],
    '/admin/products/create/do' => [ProductController::class, 'create'],
];
