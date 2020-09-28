<?php

Route::get('/', function () {
    return view('backend.welcome');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('/load_product', 'HtmlController@loadProduct');
Route::post('/onOrOff', 'ProductAjaxController@onOrOff');
Route::resource('home/ajaxproducts', 'ProductAjaxController');
Route::resource('ajaxproducts', 'ProductAjaxController');
