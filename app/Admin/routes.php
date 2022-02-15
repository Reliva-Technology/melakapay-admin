<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('agencies', AgencyController::class);
    $router->resource('services', ServiceController::class);
    $router->resource('agency-api', AgencyServiceController::class);
    $router->resource('contacts', ContactController::class);
    $router->resource('contact-details', ContactDetailController::class);
    $router->resource('users', UserController::class);
    $router->resource('favourites', FavouriteController::class);
    $router->resource('feedback', FeedbackController::class);
    $router->resource('transactions', TransactionController::class);
    $router->resource('payments', PaymentController::class);
    $router->resource('profiles', ProfileController::class);
    $router->resource('scheduler', SchedulerController::class);
    $router->resource('faqs', FaqController::class);
    $router->resource('agency-details', AgencyDetailsController::class);
    $router->resource('uploads', UploadController::class);
    $router->resource('carian-persendirian', CarianPersendirianController::class);
    $router->get('/carian-persendirian/print-carian-persendirian/{id}','CarianPersendirianController@print');

    $router->get('/api/service','SchedulerController@service');

});
