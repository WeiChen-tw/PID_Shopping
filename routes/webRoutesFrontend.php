<?php

Route::get('/', function () {
    return view('frontend.welcome');
});

Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/msgBoard', 'MsgController@index');


Route::get('/load_product', 'HtmlController@loadProduct');
Route::resource('home/ajaxshopcart', 'ShoppingCartAjaxController');
Route::resource('home/ajaxorderdetail', 'OrderDetailAjaxController');
Route::resource('home/ajaxuser', 'UserAjaxController');
Route::post('/editProfile', 'UserAjaxController@editProfile');
Route::get('/getOrder', 'OrderDetailAjaxController@getOrder');
Route::post('/getOrderDetail', 'OrderDetailAjaxController@getOrderDetail');
Route::post('/getOrderDiscount', 'OrderDetailAjaxController@calc');
Route::delete('/cancelOrder', 'OrderDetailAjaxController@cancelOrder');
Route::delete('/cancelOrderDetail', 'OrderDetailAjaxController@cancelOrderDetail');
Route::post('/receipt', 'OrderDetailAjaxController@receipt');
Route::post('/returnOrder', 'OrderDetailAjaxController@returnOrder');
Route::post('/returnOrderDetail', 'OrderDetailAjaxController@returnOrderDetail');
Route::get('/getProduct/{id}', 'ShoppingCartAjaxController@getProduct');