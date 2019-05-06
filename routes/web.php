<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('/mi-chat')->group(function () {

    // AUTH
    Route::group(['middleware' => ['guest']], function () {
        Route::view('/splash', 'splash')->name('splash');
        Route::view('/login', 'auth.login')->name('login');
        Route::view('/register', 'auth.register')->name('register');
    });

    Route::prefix('/auth')->group(function () {
        Route::post('/login', 'Auth\LoginController@login')->name('auth.login');
        Route::post('/logout', 'Auth\LoginController@logout')->name('auth.logout');
        Route::post('/register', 'Auth\RegisterController@create')->name('auth.register');
    });
    //AUTH END

    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::prefix('/web')->group(function () {
            Route::prefix('/users')->group(function () {
                Route::post('/search', 'UserController@search')->name('user.search');
                Route::get('/getLoggedInUser', 'UserController@getLoggedInUser')->name('user.current');
                Route::post('/updateProfile', 'UserController@updateProfile')->name('user.update');
                Route::post('/setProfileImage', 'UserController@setProfileImage')->name('user.image.save');
                Route::get('/profileImage/{filename}', function ($filename) {
                    return response()->file(UserController::getProfileImage($filename));
                })->name('user.image.get');
            });
            Route::prefix('/chats')->group(function () {
                Route::post('/save', 'ChatController@save')->name('chat.save');
                Route::post('/search', 'ChatController@search')->name('chat.search');
                Route::get('/getById/{id}', 'ChatController@getById')->name('chat.id');
                Route::get('/getMessages/{id}', 'MessageController@getByChat')->name('chat.messages');
                Route::post('/updatePermissions', 'ChatController@updatePermissions')->name('chat.permissions.update');
                Route::post('/leave', 'ChatController@leave')->name('chat.leave');
            });
            Route::prefix('/messages')->group(function () {
                Route::post('/save', 'MessageController@save')->name('messages.save');
                Route::post('/delete', 'MessageController@delete')->name('messages.delete');
                Route::post('/update', 'MessageController@update')->name('messages.update');
                Route::post('/getByChat', 'MessageController@getByChat')->name('messages.chat');
            });
            Broadcast::routes();
        });

        Route::view('/home', 'home')->name('home');
        Route::view('/profile', 'profile')->name('profile');
    });

    Route::redirect('', '/mi-chat/splash');

});
