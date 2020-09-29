<?php

Route::get('/', function () {
    return view('backend.welcome');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('/load_product', 'HtmlController@loadProduct');
Route::post('/onOrOff', 'ProductAjaxController@onOrOff');
Route::resource('home/ajaxproducts', 'ProductAjaxController');
Route::resource('home/ajaxcategory', 'CategoryAjaxController');
Route::get('/getCategoryData', 'CategoryAjaxController@getCategoryData');
//Route::resource('ajaxproducts', 'ProductAjaxController');
//Route::resource('home/ajaxproducts', 'ProductAjaxController');
