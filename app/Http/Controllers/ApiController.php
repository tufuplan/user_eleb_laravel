<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    //商家列表接口
    public function shops()
    {
        $shops = DB::table('businesses')->join('businesses_info','businesses.id','=','businesses_info.id')->get();
        foreach ($shops as $shop){
            $shop->shop_name = $shop->account;
            $shop->shop_img  = $shop->logo;
            $shop->brand==1?true:false;
            $shop->on_time==1?true:false;
            $shop->fengniao==1?true:false;
            $shop->bao==1?true:false;
            $shop->piao==1?true:false;
            $shop->zhun==1?true:false;
            $shop->distance = 767;
            $shop->estimate_time= 40;
        }

        return $shops;
    }
    //单个商家接口
    public function shop(Request $request)
    {
        $id = $request->id??7;
        $shop = DB::table('businesses')->join('businesses_info','businesses.id','=','businesses_info.id')->where('businesses.id','=',$id)->get()->first();
        $shop->shop_name = $shop->account;
        $shop->shop_img  = $shop->logo;
        $shop->brand==1?true:false;
        $shop->on_time==1?true:false;
        $shop->fengniao==1?true:false;
        $shop->bao==1?true:false;
        $shop->piao==1?true:false;
        $shop->zhun==1?true:false;
        $shop->distance = 767;
        $shop->estimate_time= 40;
        $shop->evaluate = [
            [ "user_id"=> 12344,
                "username"=> "w******k",
                "user_img"=> "http://www.homework.com/images/slider-pic4.jpeg",
                "time"=> "2017-2-22",
                "evaluate_code"=> 1,
                "send_time"=> 30,
                "evaluate_details"=> "不怎么好吃"],
            [
                "user_id"=> 12344,
                "username"=> "w******k",
                "user_img"=> "http://www.homework.com/images/slider-pic4.jpeg",
                "time"=> "2017-2-22",
                "evaluate_code"=> 4.5,
                "send_time"=> 30,
                "evaluate_details"=> "很好吃"

            ],
        ];
        $fcategories = DB::table('fcategories')->where('business_id','=',$id)->get();
        $commodity = [];
        foreach ($fcategories as $fcategory){
            $fcategory->description = $fcategory->detail;
            $fcategory->type_accumulation = 'c2';
            $good_list = [];
            $dishes = DB::table('dishes')->where('fcategory_id','=',$fcategory->id)->get();
            foreach ($dishes as $dish){
                $dish->goods_id = $dish->id;
                $dish->goods_name = $dish->name;
                $dish->rating = '5';
                $dish->goods_price = $dish->price;
                $dish->description = $dish->detail;
                $dish->month_sales = 723;
                $dish->rating_count = 65;
                $dish->tips = "整块无骨鸡腿肉, 浓郁汉堡酱，香脆鲜辣多汁。";
                $dish->satisfy_count = 6;
                $dish->satisfy_rate = 8;
                $dish->goods_img = $dish->cover;
                $good_list[] = $dish;
            }
            $fcategory->goods_list = $good_list;
            $commodity[] = $fcategory;
        }
        $shop->commodity = $commodity;

        return json_encode($shop);
    }
}
