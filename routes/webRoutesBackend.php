<?php

Route::get('/', function () {
    return view('backend.welcome');
});

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');
Route::get('load_product', 'HtmlController@loadProduct');
//--
Route::resource('home/ajaxproducts', 'ProductAjaxController');
Route::resource('home/ajaxcategory', 'CategoryAjaxController');
Route::resource('home/ajaxuser', 'UserAjaxController');
Route::resource('home/ajaxdiscount', 'DiscountAjaxController');
//--
Route::post('setProductCategory', 'CategoryAjaxController@setProductCategory');

//--
Route::post('setProductDiscount', 'DiscountAjaxController@setProductDiscount');

//--
Route::post('getProductData', 'ProductAjaxController@getProductData');
Route::post('onOrOff', 'ProductAjaxController@onOrOff');
Route::post('banOrUnban', 'UserAjaxController@banOrUnban');
//--
Route::get('/getOrder', 'OrderDetailAjaxController@getOrder');
Route::post('/getOrderDetail', 'OrderDetailAjaxController@getOrderDetail');
Route::post('/ship', 'OrderDetailAjaxController@ship');
Route::post('/returnOrder', 'OrderDetailAjaxController@returnOrder');
Route::post('/returnOrderDetail', 'OrderDetailAjaxController@returnOrderDetail');
Route::post('/upload', 'ProductAjaxController@uploadImg');
Route::post('/setConfig', 'ConfigController@setConfig');
Route::get('/getConfig', 'ConfigController@getConfig');