<?php

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\CheckoutController;

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

    /**
     * Admin Product Routes
     */
    '/admin/products/{id}/edit' => [ProductController::class, 'updateForm'],
    '/admin/products/{id}/edit/do' => [ProductController::class, 'update'],
    '/admin/products/create' => [ProductController::class, 'createForm'],
    '/admin/products/create/do' => [ProductController::class, 'create'],
    '/admin/products/{id}/delete' => [ProductController::class, 'delete'],

    /**
     * Admin User Routes
     */
    '/admin/users/{id}/edit' => [UserController::class, 'updateForm'],
    '/admin/users/{id}/edit/do' => [UserController::class, 'update'],
    '/admin/users/{id}/delete' => [UserController::class, 'delete'],

    /**
     * Checkout Routes
     */
    '/checkout' => [CheckoutController::class, 'paymentForm'],
    '/checkout/payment/do' => [CheckoutController::class, 'handlePaymentForm'],
    '/checkout/address' => [CheckoutController::class, 'addressForm'],
];
