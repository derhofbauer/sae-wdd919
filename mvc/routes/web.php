<?php

use App\Controllers\AddressController;
use App\Controllers\AdminController;
use App\Controllers\ApiDemoController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CategoryController;
use App\Controllers\CheckoutController;
use App\Controllers\EmailController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\PaymentController;
use App\Controllers\PostController;
use App\Controllers\ProductController;
use App\Controllers\ProfileController;
use App\Controllers\RatingController;
use App\Controllers\SearchController;
use App\Controllers\UserController;

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
    '/products/category/{id}' => [HomeController::class, 'category'],
    '/blog' => [HomeController::class, 'blog'],
    '/blog/{id}' => [HomeController::class, 'post'],
    '/blog/{id}/{slug}' => [HomeController::class, 'post'],

    /**
     * Product Routes
     */
    '/products/{id}' => [ProductController::class, 'show'],
    '/products/{id}/add-rating' => [RatingController::class, 'create'],

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
     * Admin Order Routes
     */
    '/admin/orders/{id}/edit' => [OrderController::class, 'updateForm'],
    '/admin/orders/{id}/edit/do' => [OrderController::class, 'update'],

    /**
     * Admin Category Routes
     */
    '/admin/categories/{id}/edit' => [CategoryController::class, 'updateForm'],
    '/admin/categories/{id}/edit/do' => [CategoryController::class, 'update'],
    '/admin/categories/create' => [CategoryController::class, 'createForm'],
    '/admin/categories/create/do' => [CategoryController::class, 'create'],

    /**
     * Admin Blog Post Routes
     */
    '/admin/posts/{id}/edit' => [PostController::class, 'updateForm'],
    '/admin/posts/{id}/edit/do' => [PostController::class, 'update'],
    '/admin/posts/create' => [PostController::class, 'createForm'],
    '/admin/posts/create/do' => [PostController::class, 'create'],

    /**
     * Checkout Routes
     */
    '/checkout' => [CheckoutController::class, 'paymentForm'],
    '/checkout/payment/do' => [CheckoutController::class, 'handlePaymentForm'],
    '/checkout/address' => [CheckoutController::class, 'addressForm'],
    '/checkout/address/do' => [CheckoutController::class, 'handleAddressForm'],
    '/checkout/final' => [CheckoutController::class, 'finalForm'],
    '/checkout/finish' => [CheckoutController::class, 'finish'],

    /**
     * Profile Routes
     */
    '/profile' => [ProfileController::class, 'profileForm'],
    '/profile/do' => [ProfileController::class, 'profileUpdate'],
    '/profile/orders' => [ProfileController::class, 'orders'],

    '/profile/addresses/{id}/edit' => [AddressController::class, 'updateForm'],
    '/profile/addresses/{id}/edit/do' => [AddressController::class, 'update'],

    '/profile/payments/{id}/edit' => [PaymentController::class, 'updateForm'],
    '/profile/payments/{id}/edit/do' => [PaymentController::class, 'update'],

    /**
     * Search Routes
     */
    '/search' => [SearchController::class, 'search'],

    /**
     * Contact form Routes
     */
    '/contact' => [EmailController::class, 'contactForm'],
    '/contact/send' => [EmailController::class, 'contact'],

    /**
     * API Demos
     */
    '/api-demos/colors' => [ApiDemoController::class, 'colors']
];
