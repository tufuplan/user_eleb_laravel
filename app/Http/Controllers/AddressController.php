<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    //
    public function store(Request $request)
    {
        //数据验证
        $result = preg_match('/^[1][3,4,5,7,8][0-9]{9}$/',$request->tel);
        if($result==0){
            return [
                'status'=>false,
                'message'=>'请输入正确的手机号'
            ];
        }
       $validator=  Validator::make($request->all(),[
            //验证规则
           'name'=>'required|max:6',
           'provence'=>'required',
           'city'=>'required',
           'area'=>'required',
           'detail_address'=>'required'
        ],[
            //错误信息
           'name.required'=>'收件人姓名不能为空',
           'name.max'=>'姓名不能超过六个字符',
           'provence.required'=>'所在省不能为空',
           'city.required'=>'所在城市不能为空',
           'area.required'=>'所在区不能为空',
           'deatil_address'=>'详细地址不能空'
        ]);
        if($validator->fails()){
            $errors = $validator->errors();
            return [
                'status'=>'false',
                'message'=>$errors->first(),
            ];
        }
        //数据验证完成
        $user_id = Auth::user()->id;
        Address::create([
            'Receiver'=>$request->name,
            'province'=>$request->provence,
            'city'=>$request->city,
            'area'=>$request->area,
            'detail'=>$request->detail_address,
            'phone'=>$request->tel,
            'user_id'=>$user_id
        ]);
        return [
            'status'=>true,
            'message'=>'添加地址成功'
        ];
    }
    //地址列表
    public function index()
    {
        $user_id = Auth::user()->id;
        $addresses = DB::table('addresses')->where('user_id','=',$user_id)->get();
        foreach ($addresses as $address){
            $address->provence = $address->province;
            $address->detail_address = $address->detail;
            $address->name = $address->Receiver;
            $address->tel = $address->phone;
        }
        return $addresses;
    }
    //修改地址
    public function edit(Request $request)
    {

        $address = DB::table('addresses')->where('id','=',$request->id)->get()->first();
        $address->provence = $address->province;
        $address->detail_address = $address->detail;
        $address->name = $address->Receiver;
        $address->tel = $address->phone;
        return json_encode($address);
    }
    //保存修改
    public function update(Request $request)
    {
        //数据验证
        $result = preg_match('/^[1][3,4,5,7,8][0-9]{9}$/',$request->tel);
        if($result==0){
            return [
                'status'=>false,
                'message'=>'请输入正确的手机号'
            ];
        }
        $validator=  Validator::make($request->all(),[
            //验证规则
            'name'=>'required|max:6',
            'provence'=>'required',
            'city'=>'required',
            'area'=>'required',
            'detail_address'=>'required'
        ],[
            //错误信息
            'name.required'=>'收件人姓名不能为空',
            'name.max'=>'姓名不能超过六个字符',
            'provence.required'=>'所在省不能为空',
            'city.required'=>'所在城市不能为空',
            'area.required'=>'所在区不能为空',
            'deatil_address'=>'详细地址不能空'
        ]);
        if($validator->fails()){
            $errors = $validator->errors();
            return [
                'status'=>'false',
                'message'=>$errors->first(),
            ];
        }
        $result = DB::table('addresses')
            ->where('id', $request->id)
            ->update([
                'Receiver' => $request->name,
                'phone'=>$request->tel,
                'city'=>$request->city,
                'province'=>$request->provence,
                'area'=>$request->area,
                'detail'=>$request->detail_address
                ]);

         return  [
            'status'=>"true",
            'message'=>'修改信息成功'
        ];
    }
}
