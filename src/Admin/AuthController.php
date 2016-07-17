<?php 
namespace Friparia\Admin;

use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;
use Illuminate\Http\Request;

class AuthController extends LaravelController{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    public function login(Request $request){
        return view('admin::login');
    }

    public function dologin(Request $request)
    {
        $name = $request->input('name');
        $password = $request->input('password');
        if (Auth::attempt(['name' => $name, 'password' => $password])) {
            return redirect("/admin/");
        }
        return redirect("/admin/auth/login")->withInput()->with('error', "用户名或密码错误");
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect("/admin/auth/login")->withInput()->with('error', "注销成功！");
    }


}
