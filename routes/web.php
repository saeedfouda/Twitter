<?php

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

Route::middleware('guest')->group(function(){
    Route::view('/', 'welcome');
    Route::post('password/options', 'Auth\ForgotPasswordController@showResetOptionsForm')->name('password.options');
});

Auth::routes();

// Pages
Route::get('/about', function(){
    return 'This will be about page';
})->name('about');

Route::middleware('auth')->group(function(){
    Route::get('/home', 'HomeController@index')->name('home'); // Done

    // Notifications
    Route::get('/notifications', 'NotificationsController@index')->name('notifications'); // Not yet

    // Messages
    Route::get('/messages', 'MessagesController@index')->name('messages.index'); // Done
    Route::get('/moreUnread/{id}', 'MessagesController@moreUnread');

    Route::get('/messages/{id}/partner', 'MessagesController@partner');

    Route::get('/messages/{username}', 'MessagesController@show')->name('messages.show');
    Route::post('/IveSeenTheMessage/{id}', 'MessagesController@IveSeenThis');

    Route::post('/newmessage', 'MessagesController@store');


    // Settings
    Route::redirect('/settings', '/settings/account')->name('settings.edit'); // Done
    Route::get('/settings/account', 'UsersController@settings')->name('settings.account'); // Done
    Route::post('/settings/account', 'UsersController@accountUpdate'); // Done
    Route::post('/checkUnique', 'UsersController@checkUnique'); // Done
    Route::get('/settings/safety', 'UsersController@editPassword')->name('settings.safety'); // Done
    Route::post('/settings/safety', 'UsersController@updatePassword'); // Done

    // Tweets
    Route::get('/tweets', 'TweetsController@index'); // Done
    Route::post('/tweet', 'TweetsController@store'); // Done
    Route::post('/moreTweets', 'TweetsController@moreTweets'); // Done

    Route::get('/profile/{id}/tweets', 'UsersController@index'); // Done
    Route::post('/profile/{id}/moreTweets', 'UsersController@moreTweets'); // Done

    Route::get('/comments/{id}', 'CommentsController@index'); // Done
    Route::post('/comment/{id}', 'CommentsController@store'); // Done
    Route::post('/moreComment', 'CommentsController@moreComments'); // Done

    // Like & unlike
    Route::post('/like/{id}', 'TweetsController@like'); // Done
    Route::post('/unlike/{id}', 'TweetsController@unlike'); // Done

    // Delete
    Route::delete('/delete/{id}', 'TweetsController@destroy'); // Done

    // Profile
    // picture
    Route::post('/profilepicture', 'UsersController@photo'); // Done
    Route::post('/removepicture', 'UsersController@removePhoto'); // Done
    Route::post('/profilecover', 'UsersController@cover'); // Done
    Route::post('/removecover', 'UsersController@removeCover'); // Done

    // Following & Unfollowing
    Route::post('/follow/{id}', 'UsersController@follow'); // Done
    Route::post('/unfollow/{id}', 'UsersController@unfollow'); // Done

    // Search
    Route::get('/search', 'UsersController@search');
    Route::post('/moreresults', 'UsersController@searchMore');

    Route::get('/{username}/status/{id}', 'TweetsController@show')->name('tweet.show'); // Done
    Route::get('/{username}', 'UsersController@show')->name('user.show'); // Done
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
