<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.form');
    }

    public function login(UserLoginRequest $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        $remember = $request->has('remember');
        if (Auth::attempt($data, $remember)) {
            // Xóa session cũ
            $session = Session::where('user_id', Auth::id())->delete();

            // Tạo phiên đăng nhâp mới
            session()->put('user_id', Auth::id());

            //Đăng nhập thành công
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard')->with('message', 'Đăng nhập thành công!');
            } else {
                return redirect()->route('client.home')->with('message', 'Đăng nhập thành công!');
            }
        } else {
            //Đăng nhập thất bại
            return redirect()->route('auth')->with('message', 'Email hoặc mật khẩu không đúng!');
        }
    }
    public function register(UserRegisterRequest $request)
    {
        try {
            // Tạo dữ liệu người dùng mới
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 2 // Giả sử role_id 2 là "người dùng"
            ];
            // Lưu người dùng vào cơ sở dữ liệu
            User::create($data);

            // Sau khi đăng ký thành công, chuyển hướng về trang auth với thông báo thành công
            return redirect()->route('auth')->with('message', 'Đăng ký thành công! Mời đăng nhập');
        } catch (\Throwable $th) {
            // Redirect lại trang auth, giữ input và active tab "register"
            return redirect()->route('auth')
                ->withInput()
                ->withErrors('error', 'Đăng ký không thành công!')
                ->with('active_tab', 'register'); // Chuyển active tab sang Register
        }
    }

}
