<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    //添加商品到购物车
    public function addCart(Request $request)
    {

        $goodsList = $request->goodsList;
        $countList = $request->goodsCount;
        $array = array_combine($goodsList,$countList);
        $user_id = Auth::user()->id;
        DB::table('carts')->truncate();
        foreach ($array as $key=>$value){
            Cart::create([
                'goods_list'=>$key,
                'goods_count'=> $value,
                'status'=>0,
                'user_id'=>$user_id,
            ]);
        }
        return [
            'status'=>'true',
            'message'=>'添加购物车成功'
        ];
    }
    //使用购物车中的数据
    public function getCart()
    {

        $user_id = Auth::user()->id;
        $goods_list = DB::table('carts')
            ->join('dishes', 'carts.goods_list', '=', 'dishes.id')->where('user_id',$user_id)
            ->get();
        $totalCost =0;


        foreach ($goods_list as $good){
            $good->goods_id = $good->goods_list;
            $good->goods_name = $good->name;
            $good->goods_img = $good->cover;
            $good->goods_price = $good->price;
            $good->amount = $good->goods_count;
            $totalCost += ($good->price)*($good->goods_count);
        }
        $goods_list->totalCost=$totalCost;
        $str =['goods_list'=>$goods_list,'totalCost'=>$totalCost];


        return json_encode($str);




    }
}
