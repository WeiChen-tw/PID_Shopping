<?php

Route::get('/', function () {
    return view('frontend.welcome');
});
// Route::get('/', 'HtmlController@loadProduct');
Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/profile', 'HomeController@index')->name('home');
Route::get('/msgBoard', 'MsgController@index');
Route::post('/msgBoard/sendMsg', 'MsgController@store');
Route::post('/msgBoard/editMsg', 'MsgController@editMsg');
Route::post('/msgBoard/edit/{id}', 'MsgController@edit');
Route::post('/msgBoard/reply', 'MsgController@reply');
Route::post('/msgBoard/getMsg/{id}', 'MsgController@getMsg');
Route::delete('/msgBoard/del/{id}', 'MsgController@destroy');


Route::get('/load_product', 'HtmlController@loadProduct');
Route::get('/getCategory', 'HtmlController@getCategory');
Route::post('/searchCategory', 'HtmlController@searchCategory');
Route::post('/searchKeyword', 'HtmlController@searchKeyword');
Route::post('/orderBy', 'HtmlController@orderBy');

Route::resource('home/ajaxshopcart', 'ShoppingCartAjaxController');
Route::get('/getProduct/{id}', 'ShoppingCartAjaxController@getProduct');

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

