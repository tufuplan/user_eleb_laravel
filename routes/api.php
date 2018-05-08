<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/sms','SessionController@sendMsg');
Route::post('/regist','SessionController@regist');
Route::post('/loginCheck','SessionController@login');
//修改密码暂时不做
Route::post('/changePassword','SessionController@change');
//忘记密码
Route::post('/forgetPassword','SessionController@forget');
//用户添加收货地址
Route::post('/addAddress','AddressController@store');
//用户查看收货地址列表接口
Route::get('/addressList','AddressController@index');
//用户修改某个地址信息
Route::get('/address','AddressController@edit');
Route::post('/editAddress','AddressController@update');
//添加商品到购物车
Route::post('/addCart','CartController@addCart');
Route::get('/cart','CartController@getCart');
//订单
Route::post('/addorder','OrderController@add');
//显示所有订单
Route::get('/orderList','OrderController@orderList');
//显示指定单个订单
Route::get('/order','OrderController@show');
