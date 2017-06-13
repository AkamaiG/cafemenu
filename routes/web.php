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

//cafepanel
Route::get('/', 'OwnerController@index');
Route::get('/reviews', 'OwnerController@getReviews');
Route::post('/api/owner/login', 'OwnerController@login');
Route::get('/api/owner/logout', 'OwnerController@logout');

//post request API
Route::post('/api/get/cafelist', 'CafeController@getCafeList');
Route::post('/api/get/userinfo', 'CafeController@getUserInfoVk');
Route::post('/api/get/cafelocationdata', 'CafeController@getcafedata');
Route::post('/api/get/cafeiddata', 'CafeController@getcafemenu');
Route::post('/api/get/addreview', 'CafeController@addReviews');
Route::post('/api/get/delreview', 'CafeController@delReviews');
Route::post('/api/get/reviews', 'CafeController@getReviews');
Route::post('/api/get/userreviews', 'CafeController@userReviews');

//cron rating
Route::get('/api/get/ratingcron', 'CafeController@updRating');