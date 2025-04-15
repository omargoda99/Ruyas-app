<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::group(['middleware' => ['api', 'checkblocked']], function () {
    // Welcome and Terms
    Route::get('/', 'App\Http\Controllers\WelcomeController@welcome');
    Route::get('/terms', 'App\Http\Controllers\TermsController@terms');

    // Authentication Routes
    Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->middleware('auth:api');

    // Activation Routes
    Route::get('/activate', 'App\Http\Controllers\Auth\ActivateController@initial');
    Route::get('/activate/{token}', 'App\Http\Controllers\Auth\ActivateController@activate');
    Route::post('/activation/resend', 'App\Http\Controllers\Auth\ActivateController@resend');
    Route::get('/exceeded', 'App\Http\Controllers\Auth\ActivateController@exceeded');

    // Socialite Routes
    Route::get('/social/redirect/{provider}', 'App\Http\Controllers\Auth\SocialController@getSocialRedirect');
    Route::get('/social/handle/{provider}', 'App\Http\Controllers\Auth\SocialController@getSocialHandle');

    // Reactivation
    Route::post('/re-activate/{token}', 'App\Http\Controllers\RestoreUserController@userReActivate');
});

// Authenticated Routes
Route::group(['middleware' => ['auth:api', 'activated', 'activity', 'checkblocked']], function () {
    Route::get('/activation-required', 'App\Http\Controllers\Auth\ActivateController@activationRequired');

    // Home route
    Route::get('/home', 'App\Http\Controllers\UserController@index');

    // Profile routes
    Route::get('/profile/{username}', 'App\Http\Controllers\ProfilesController@show');
    Route::post('/profile', 'App\Http\Controllers\ProfilesController@create');
    Route::put('/profile/{username}', 'App\Http\Controllers\ProfilesController@update');
    Route::put('/profile/{username}/account', 'App\Http\Controllers\ProfilesController@updateUserAccount');
    Route::put('/profile/{username}/password', 'App\Http\Controllers\ProfilesController@updateUserPassword');
    Route::delete('/profile/{username}', 'App\Http\Controllers\ProfilesController@deleteUserAccount');

    // Avatar routes
    Route::get('/images/profile/{id}/avatar/{image}', 'App\Http\Controllers\ProfilesController@userProfileAvatar');
    Route::post('/avatar/upload', 'App\Http\Controllers\ProfilesController@upload');
});

// Admin Routes
Route::group(['middleware' => ['auth:api', 'activated', 'role:admin', 'activity', 'checkblocked']], function () {
    // User management
    Route::get('/users', 'App\Http\Controllers\UsersManagementController@index');
    Route::post('/users', 'App\Http\Controllers\UsersManagementController@store');
    Route::get('/users/{user}', 'App\Http\Controllers\UsersManagementController@show');
    Route::put('/users/{user}', 'App\Http\Controllers\UsersManagementController@update');
    Route::delete('/users/{user}', 'App\Http\Controllers\UsersManagementController@destroy');
    Route::post('/users/search', 'App\Http\Controllers\UsersManagementController@search');

    // Deleted users
    Route::get('/users/deleted', 'App\Http\Controllers\SoftDeletesController@index');
    Route::get('/users/deleted/{user}', 'App\Http\Controllers\SoftDeletesController@show');
    Route::put('/users/deleted/{user}', 'App\Http\Controllers\SoftDeletesController@update');
    Route::delete('/users/deleted/{user}', 'App\Http\Controllers\SoftDeletesController@destroy');

    // Themes
    Route::get('/themes', 'App\Http\Controllers\ThemesManagementController@index');
    Route::post('/themes', 'App\Http\Controllers\ThemesManagementController@store');
    Route::get('/themes/{theme}', 'App\Http\Controllers\ThemesManagementController@show');
    Route::put('/themes/{theme}', 'App\Http\Controllers\ThemesManagementController@update');
    Route::delete('/themes/{theme}', 'App\Http\Controllers\ThemesManagementController@destroy');

    // Admin tools
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/routes', 'App\Http\Controllers\AdminDetailsController@listRoutes');
});

// PHP info
Route::get('/phpinfo', function () {
    return phpinfo();
});
