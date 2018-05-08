<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //添加订单接口
    public function add(Request $request)

    {
//        传过来的是地址id
        $address_id = $request->address_id;
        $user_id = DB::table('addresses')->select('user_id')->where('id',$address_id)->get()->first()->user_id;//得到那个用户
        //得到该订单下所有商品信息
        $good_list = DB::table('carts')->join('dishes','carts.goods_list','=','dishes.id')->where('user_id',$user_id)->get();
        $shop_id = $good_list->first()->business_id;
        //得到地址信息
        $addresses = DB::table('addresses')->where('id',$address_id)->get()->first();
        $prefix = date('Y-m',time());
        $order_code =$prefix.uniqid() ;

        //创建订单记录
//        $id = DB::table('orders')->insertGetId(
//            [ 'order_code'=>$order_code,
//           'order_status'=>0,
//           'city'=>$addresses->city,
//           'area'=>$addresses->area,
//           'province'=>$addresses->province,
//           'detail'=>$addresses->detail,
//           'Receiver'=>$addresses->Receiver,
//           'phone'=>$addresses->phone,
//              ''
//                ]
//        );
        $user_id = Auth::user()->id;
        $Order = Order::create([
            'order_code'=>$order_code,
          'order_status'=>0,
          'city'=>$addresses->city,
          'area'=>$addresses->area,
          'province'=>$addresses->province,
          'detail'=>$addresses->detail,
          'Receiver'=>$addresses->Receiver,
          'phone'=>$addresses->phone,
            'user_id'=>$user_id,
            'shop_id'=>$shop_id
        ]);
        $id = $Order->id;
        //创建中间表记录,记录该订单和商品的关系
        foreach ($good_list as $good){
            DB::table('order_dishes')->insert([
                [
                    'order_id' =>$id,
                    'goods_id' => $good->id,
                    'good_name'=>$good->name,
                    'good_image'=>$good->cover,
                    'good_price'=>$good->price,
                    'amount'=>$good->goods_count,
                ],
            ]);
        }
        return [
            'status'=>'true',
            'message'=>'生成订单成功',
            'order_id'=>$id
        ];
    }
    //显示指定订单
    public function show(Request $request)
    {
        $id = $request->id;//订单id;
        $order = DB::table('orders')->where('id',$id)->get()->first();
        //根据商品找到shop
        $good_id = DB::table('order_dishes')->where('order_id',$id)->get()->first()->goods_id;
        //商户id
        $good = DB::table('dishes')->where('id',$good_id)->get()->first();
        $shop_id = $good->business_id;
        $shop = DB::table('businesses')->where('id',$shop_id)->get()->first();
        $shop_name = $shop->account;
        $shop_img= $shop->logo;


        $good_list = DB::table('order_dishes')->where('order_id',$id)->get();
        $order_price = 0;
        foreach ($good_list as $a_good){
            $a_good->goods_name= $a_good->good_name;
            $a_good->goods_img = $a_good->good_image;
            $a_good->goods_price = $a_good->good_price;
            $order_price += ($a_good->good_price)*($a_good->amount);
        }

        $str = [
            "id"=>$id,
        "order_code"=>$order->order_code,
        "order_birth_time"=>$order->created_at,
        "order_status"=>$order->order_status==0?'代付款':'已付款',
        "shop_id"=>$shop_id,
        "shop_name"=> $shop_name,
        "shop_img"=> $shop_img,
            'goods_list'=>$good_list,
            'order_price'=>$order_price,
            'order_address'=>($order->province.$order->city.$order->area.$order->detail)
        ];
        return json_encode($str);


    }
//    订单列表
    public function orderList()
    {
        $user_id = Auth::user()->id;
        $orders = DB::table('orders')->where('user_id',$user_id)->get();
        //
        foreach ($orders as $order){
            $order_dishes = DB::table('order_dishes')->where('order_id',$order->id)->get();
            $goods_list=[];
            $total_price = 0;
            foreach ($order_dishes as $order_dish){
                    $order_dish->goods_price = $order_dish->good_price;
                    $order_dish->goods_image = $order_dish->good_image;
                    $order_dish->goods_name = $order_dish->good_name;
                    $goods_list[] =$order_dish ;
                    $total_price += ($order_dish->good_price)*($order_dish->amount);
            }
            $order->order_birth_time = $order->created_at;
            //找到商品的所在商户
           $shop =  DB::table('businesses')->where('id',$order->shop_id)->get()->first();

           $order->shop_name = $shop->account;
           $order->shop_img = $shop->logo;
           $order->goods_list =$goods_list;
           $order->order_price = $total_price;
           $order->order_address = $order->province.$order->city.$order->area.$order->detail;
        }
        //
        return json_encode($orders);

    }

}
