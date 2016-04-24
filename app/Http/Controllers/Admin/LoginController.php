<?php

namespace App\Http\Controllers\Admin;
use App\Http\Model\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

require_once 'resources/org/code/Code.class.php';

class LoginController extends CommonController
{
    public function login()
    {
        //echo Crypt::decrypt("eyJpdiI6ImI3V0lqMUI5MmlxSW1xR2hxdlwvVHR3PT0iLCJ2YWx1ZSI6Ik9ZVCtibkRUSXBMVGZYYWFsNVFvRHc9PSIsIm1hYyI6IjgzOGMwYjY3ZTEwYWJkYjI1MjMzNWM4OGM1MmQzNTY4NDM0N2Q2NmNkMGE3ODUyNTNlYzQwODQ5MDljN2MxZWEifQ==");die;

        if($input = Input::all()){
            $code = new \Code;
            $_code = $code->get();
            if(strtoupper($input['code'])!=$_code){
                return back()->with('msg','验证码错误！');
            }
            $user = User::first();
            //dd($user);
            if($user->user_name != $input['user_name'] || Crypt::decrypt($user->user_password)!= $input['user_password']){
                return back()->with('msg','用户名或者密码错误！');
            }

           session(['user'=>$user]);
//            dd(session('user'));
            return redirect('admin/index');

        }else {
            return view('admin.login');
        }
    }

    public function quit()
    {
        session(['user'=>null]);
        return redirect('admin/login');
    }

    /**
     * 更改密码
     */
    public function pass()
    {
        if($input = Input::all()){

            $rules = [
                'password' => 'required|between:6,20|confirmed',
            ];

            $message = [
                'password.required' => "密码不能为空!",
                'password.between' => "新密码必须在6至20位之间!",
                'password.confirmed' => "两次密码不匹配!"
            ];

            $validator = Validator::make($input,$rules,$message);

            if($validator->passes()){
                $user = User::first();
                $_password = Crypt::decrypt($user->user_password);
                if($input['password_o'] == $_password){
                    $user->user_password = Crypt::encrypt($input['p assword']);
                    $user->update();
                    return back()->with("errors","密码修改成功!");
                }else{
                    return back()->with("errors","原密码错误!");
                }
            }else{
                //dd($validator->errors()->all());
                return back()->withErrors($validator);
            }
        }else{
            return view('admin.pass');
        }
    }

    public function code()
    {
        $code = new \Code;
        $code->make();
    }

}
