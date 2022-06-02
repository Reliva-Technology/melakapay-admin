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
    $router->get('/about', 'HomeController@about')->name('about');
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
    $router->resource('videos', VideoController::class);
    $router->resource('carian-persendirian', CarianPersendirianController::class);
    $router->get('/carian-persendirian/print-carian-persendirian/{id}','CarianPersendirianController@print');
    $router->get('/carian-persendirian/add-carian-persendirian/{id}','CarianPersendirianController@carian');
    $router->resource('log-sms', SmsLogController::class);
    $router->resource('visitors', VisitorController::class);
    $router->resource('email-logs', EmailLogController::class);
    $router->get('/email-logs/download-attachment/{id}','EmailLogController@print');
    $router->resource('ebayar', EbayarController::class);
    $router->resource('update-transactions', UpdateTransactionController::class);
    $router->resource('ebayar-transactions', EbayarTransactionController::class);
    $router->resource('update-payments', UpdatePaymentController::class);
    $router->resource('api-logs', ApiLogController::class);

    $router->get('/api/service','SchedulerController@service');

});
