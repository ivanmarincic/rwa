<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Broadcast;
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

Route::prefix('/mi-chat')->group(function () {
    Route::prefix('/api')->group(function () {
        Route::prefix('/auth')->group(function () {
            Route::post('/login', 'Auth\LoginController@loginApi');
        });
        Route::get('/users/profileImage/{filename}', function ($filename) {
            return response()->file(UserController::getProfileImage($filename));
        });
        Route::group(['middleware' => ['auth:api']], function () {
            Route::prefix('/users')->group(function () {
                Route::post('/search', 'UserController@search');
                Route::get('/getLoggedInUser', 'UserController@getLoggedInUser');
                Route::post('/updateProfile', 'UserController@updateProfileApi');
                Route::post('/setProfileImage', 'UserController@setProfileImage');
            });
            Route::prefix('/chats')->group(function () {
                Route::post('/save', 'ChatController@save');
                Route::post('/search', 'ChatController@search');
                Route::get('/getAllByUser', 'ChatController@getAllByUser');
                Route::get('/getById/{id}', 'ChatController@getById');
                Route::get('/getMessages/{id}', 'MessageController@getByChat');
                Route::post('/updatePermissions', 'ChatController@updatePermissions');
                Route::post('/getParticipants', 'ChatController@getParticipants');
                Route::post('/leave', 'ChatController@leave');
            });
            Route::prefix('/messages')->group(function () {
                Route::post('/save', 'MessageController@save');
                Route::post('/delete', 'MessageController@delete');
                Route::post('/update', 'MessageController@update');
                Route::post('/getByChat', 'MessageController@getByChat');
            });

        });
        Broadcast::routes(['middleware' => ['auth:api']]);
    });
});
