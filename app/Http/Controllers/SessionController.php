<?php

namespace App\Http\Controllers;

use App\SendSms;
use App\User;
use Illuminate\Http\Request;
use App\Sms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationData;

class SessionController extends Controller
{
    //短信验证接口
    public function sendMsg(Request $request)
    {
//        //手机号码正则验证

        $result = preg_match('/^[1][3,4,5,7,8][0-9]{9}$/',$request->tel);
        if($result==0){
            return [
                'status'=>false,
                'message'=>'请输入正确的手机号'
            ];
        }
        SendSms::sendSms($request->tel);
//        if('OK'==='OK'){
//            echo json_encode([
//                'status'=>true,
//                'message'=>'获取短信验证码成功'
//            ]) ;
//        }else{
//            return [
//                'status'=>'false',
//                'message'=>'获取短信验证码失败,请稍后再试',
//            ];
//        }


//        $this->sendMsg();
//        return '{
//            "status": "true",
//      "message": "获取短信验证码成功"
//    }';
}
    //注册接口控制器
    public function regist(Request $request)
    {
        $name = $request->name;
       $user =  DB::table('users')->where('name','=',$name)->get()->first();
       if($user!=null){
           return json_encode([
               'status'=>'false',
               'message'=>'该用户已存在'
           ]);
       }
       else{
           $sms = $request->sms;//用户传过来的验证码
           $redis =  new \Redis();
           $redis->connect('127.0.0.1', 6379);
           $tel = $request->tel;
           $redis_sms = $redis->get($tel);//redis中存在的验证码
           if($sms!=$redis_sms){
               return json_encode([
                   'status'=>'false',
                   'message'=>'您的验证码不正确'
               ]);
           }
           //验证码正确
           User::create([
               'name'=>$request->username,
               'password'=>bcrypt($request->password),
               'tel'=>$request->tel,
           ]);
           return  [
               'status'=>'true',
               'message'=>'注册成功'
           ];
       }

    }
    //登录接口控制器
    public function login(Request $request)
    {
        //数据验证
        $validator = Validator::make($request->all(),[
            //验证规则
            'name'=>'required|min:2',
            'password'=>'required'
        ],[
            'name.required'=>'用户名不能为空',
            'name.min'=>'姓名最少两个字符',
            'password.required'=>'密码不能为空',
        ]);
        //验证失败
        if($validator->fails()){
            $errors = $validator->errors();
            return  [
                'status'=>'false',
                'message'=>$errors->first(),
                'user_id'=>'',
                'username'=>''
            ];
        }

        //登录验证
        $result = Auth::attempt(['name'=>$request->name,'password'=>$request->password],$request->has('remember'));

        if($result==true){
            $id = Auth::user()->id;
            $username =Auth::user()->name;
            return [
                    "status"=>"true",
                    "message"=>"登录成功",
                    "user_id"=>"$id",
                    "username"=>"$username"
            ];
        }
        else{
            return [
                "status"=>"false",
                "message"=>"登录失败用户名或密码不正确",
                "user_id"=>"",
                "username"=>""
            ];
        }

    }
    //忘记密码接口控制器 前端存在问题 还未测试 可能出现问题
    public function forget(Request $request)
    {
        //数据验证
        //1>>>
        $result = preg_match('/^[1][3,4,5,7,8][0-9]{9}$/',$request->tel);
        if($result==0){
            return [
                'status'=>false,
                'message'=>'请输入正确的手机号'
            ];
        }
        $validotor = Validator::make($request->all(),[
            //验证规则
            'tel'=>'required',
            'password'=>'required',
            'sms'=>'required'
        ],[
            //验证注释
            'tel.required'=>'验证码不能为空',
            'passwrod.required'=>'密码不能为空',
            'sms.required'=>'短信验证码不能为空',
        ]);
        //验证失败
        if($validotor->fails()){
            $errors = $validotor->errors();
            return [
                'status'=>false,
                'message'=>$errors->first(),
            ];
        }
        //接下来验证验证码是否正确
          $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
         $code_redis = $redis->get($request->tel);
         if($request->sms!=$code_redis){
             return  [
                 'status'=>false,
                 'message'=>'验证码错误'
             ];
         }
         //判断完毕修改用户密码
        $id = Auth::user()->id;
        DB::table('users')
            ->where('id', $id)
            ->update(['password' => bcrypt($request->password)]);
         return [
             'status'=>'true',
             'message'=>'密码修改成功'
         ];

    }
    //修改密码接口控制器
    public function change(Request $request)
    {
       $password =  Auth::user()->getAuthPassword();
      $result = Hash::check($request->oldPassword,$password);
      if(!$result){
          return [
              'status'=>'false',
              'message'=>'旧密码不正确'
          ];
      }
        $id = Auth::user()->id;
        DB::table('users')
            ->where('id', $id)
            ->update(['password' => bcrypt($request->newPassword)]);
        return [
            'status'=>'true',
            'message'=>'密码修改成功'
        ];


    }


}
