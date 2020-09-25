<?php

Route::get('/', function () {
    return view('backend.welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/load_product', 'HtmlController@loadProduct');
Route::resource('ajaxproducts', 'ProductAjaxController');
