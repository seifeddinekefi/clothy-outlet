<?php

/**
 * ============================================================
 * config/routes.php
 * ============================================================
 * Declarative route definitions.
 * The $router variable is injected by public/index.php.
 *
 * Syntax:
 *   $router->get('path',  'Controller@method', 'name');
 *   $router->post('path', 'Controller@method', 'name');
 *   $router->group(['prefix' => 'admin', 'middleware' => 'AuthMiddleware'], function($r) { … });
 * ============================================================
 *
 * @var \Router $router
 */

// ─── Public Routes ──────────────────────────────────────────

$router->get('/',                     'HomeController@index',   'home');

// ─── Product Routes ─────────────────────────────────────────

$router->get('/products',             'ProductController@index',    'products');
$router->get('/products/{category}',  'ProductController@category', 'products.category');
$router->get('/product/{id}',         'ProductController@show',     'product.show');
$router->get('/product/{id}/images',  'ProductController@images',   'product.images');
$router->get('/product/{id}/sizes',   'ProductController@sizes',    'product.sizes');
$router->get('/search',               'ProductController@search',   'search');

// ─── Cart Routes ─────────────────────────────────────────────

$router->get('/cart',                 'CartController@index',   'cart');
$router->post('/cart/add',            'CartController@add',     'cart.add');
$router->post('/cart/update',         'CartController@update',  'cart.update');
$router->post('/cart/remove',         'CartController@remove',  'cart.remove');

// ─── Authentication Routes ───────────────────────────────────

$router->get('/register',             'AuthController@registerForm', 'auth.register');
$router->post('/register',            'AuthController@register');

$router->get('/login',                'AuthController@loginForm',    'auth.login');
$router->post('/login',               'AuthController@login');

$router->get('/forgot-password',      'AuthController@forgotPasswordForm', 'auth.forgot');
$router->post('/forgot-password',     'AuthController@sendResetLink',      'auth.forgot.send');
$router->get('/reset-password/{token}', 'AuthController@resetPasswordForm', 'auth.reset');
$router->post('/reset-password/{token}', 'AuthController@resetPassword',    'auth.reset.submit');

$router->get('/logout',               'AuthController@logout',       'auth.logout');

// ─── Customer Account Routes (auth-protected) ────────────────

$router->group(['prefix' => 'account', 'middleware' => 'AuthMiddleware'], function ($router): void {
    $router->get('/',         'AccountController@dashboard', 'account');
    $router->get('/orders',   'AccountController@orders',    'account.orders');
    $router->post('/orders/cancel/{id}', 'AccountController@cancelOrder', 'account.orders.cancel');
    $router->get('/profile',  'AccountController@profile',   'account.profile');
    $router->post('/profile', 'AccountController@updateProfile');

    $router->get('/wishlist', 'WishlistController@index', 'account.wishlist');
    $router->post('/wishlist/add', 'WishlistController@add', 'account.wishlist.add');
    $router->post('/wishlist/remove', 'WishlistController@remove', 'account.wishlist.remove');
    $router->post('/wishlist/toggle', 'WishlistController@toggle', 'account.wishlist.toggle');
});

// ─── Checkout Routes (guest + auth allowed) ─────────────────

$router->group(['prefix' => 'checkout', 'middleware' => 'GuestCheckoutMiddleware'], function ($router): void {
    $router->get('/',        'CheckoutController@index',   'checkout');
    $router->post('/coupon', 'CheckoutController@applyCoupon', 'checkout.coupon');
    $router->post('/coupon/remove', 'CheckoutController@removeCoupon', 'checkout.coupon.remove');
    $router->post('/place',  'CheckoutController@place',   'checkout.place');
    $router->get('/success', 'CheckoutController@success', 'checkout.success');
    $router->post('/register', 'CheckoutController@registerGuest', 'checkout.register');
});

// ─── Guest Order Tracking ────────────────────────────────────

$router->get('/order/track/{token}', 'CheckoutController@trackOrder', 'order.track');

// ─── Admin Routes (admin-protected) ─────────────────────────
// Auth is enforced by AdminMiddleware (role check included).

$router->group(['prefix' => 'admin', 'middleware' => 'AdminMiddleware'], function ($router): void {

    // ── Dashboard ────────────────────────────────────────────
    $router->get('/',          'Admin\DashboardController@index', 'admin');
    $router->get('/dashboard', 'Admin\DashboardController@index', 'admin.dashboard');

    // ── Admin Auth ───────────────────────────────────────────
    $router->get('/login',  'Admin\AuthController@loginForm', 'admin.login');
    $router->post('/login', 'Admin\AuthController@login');
    $router->get('/logout', 'Admin\AuthController@logout',    'admin.logout');

    // ── Products ─────────────────────────────────────────────
    $router->get('/products',              'Admin\ProductController@index',   'admin.products');
    $router->get('/products/create',       'Admin\ProductController@create',  'admin.products.create');
    $router->post('/products/create',      'Admin\ProductController@store',   'admin.products.store');
    $router->get('/products/edit/{id}',    'Admin\ProductController@edit',    'admin.products.edit');
    $router->post('/products/edit/{id}',   'Admin\ProductController@update',  'admin.products.update');
    $router->post('/products/delete/{id}', 'Admin\ProductController@destroy', 'admin.products.delete');

    // ── Categories ───────────────────────────────────────────
    $router->get('/categories',              'Admin\CategoryController@index',   'admin.categories');
    $router->get('/categories/create',       'Admin\CategoryController@create',  'admin.categories.create');
    $router->post('/categories/create',      'Admin\CategoryController@store',   'admin.categories.store');
    $router->get('/categories/edit/{id}',    'Admin\CategoryController@edit',    'admin.categories.edit');
    $router->post('/categories/edit/{id}',   'Admin\CategoryController@update',  'admin.categories.update');
    $router->post('/categories/delete/{id}', 'Admin\CategoryController@destroy', 'admin.categories.delete');

    // ── Orders ───────────────────────────────────────────────
    $router->get('/orders',              'Admin\OrderController@index',        'admin.orders');
    $router->get('/orders/{id}',         'Admin\OrderController@show',         'admin.orders.show');
    $router->post('/orders/{id}/status',  'Admin\OrderController@updateStatus',        'admin.orders.status');
    $router->post('/orders/{id}/payment', 'Admin\OrderController@updatePaymentStatus', 'admin.orders.payment');

    // ── Customers ────────────────────────────────────────────
    $router->get('/customers',              'Admin\CustomerController@index',   'admin.customers');
    $router->get('/customers/edit/{id}',    'Admin\CustomerController@edit',    'admin.customers.edit');
    $router->post('/customers/edit/{id}',   'Admin\CustomerController@update',  'admin.customers.update');
    $router->post('/customers/delete/{id}', 'Admin\CustomerController@destroy', 'admin.customers.delete');
    $router->get('/customers/{id}',         'Admin\CustomerController@show',    'admin.customers.show');

    // ── Coupons ─────────────────────────────────────────────
    $router->get('/coupons',              'Admin\CouponController@index',   'admin.coupons');
    $router->get('/coupons/create',       'Admin\CouponController@create',  'admin.coupons.create');
    $router->post('/coupons/create',      'Admin\CouponController@store',   'admin.coupons.store');
    $router->get('/coupons/edit/{id}',    'Admin\CouponController@edit',    'admin.coupons.edit');
    $router->post('/coupons/edit/{id}',   'Admin\CouponController@update',  'admin.coupons.update');
    $router->post('/coupons/delete/{id}', 'Admin\CouponController@destroy', 'admin.coupons.delete');

    // ── Settings ─────────────────────────────────────────
    $router->get('/settings',          'Admin\SettingsController@index',         'admin.settings');
    $router->post('/settings/store',   'Admin\SettingsController@updateStore',   'admin.settings.store');
    $router->post('/settings/account', 'Admin\SettingsController@updateAccount', 'admin.settings.account');
    $router->post('/settings/password', 'Admin\SettingsController@updatePassword', 'admin.settings.password');
});
