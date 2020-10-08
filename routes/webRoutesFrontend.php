<?php

Route::get('/', function () {
    return view('frontend.welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/load_product', 'HtmlController@loadProduct');
Route::middleware('auth')->resource('home/ajaxshopcart', 'ShoppingCartAjaxController');
Route::middleware('auth')->resource('home/ajaxorder', 'OrderAjaxController');
Route::middleware('auth')->resource('home/ajaxuser', 'UserAjaxController');
Route::post('/editProfile', 'UserAjaxController@editProfile');