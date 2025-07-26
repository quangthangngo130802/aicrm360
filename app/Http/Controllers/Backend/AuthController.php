<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function login()
    {
        return view('backend.auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ], __('request.messages'), [
            'email' => 'Email',
            'password' => 'mật khẩu'
        ]);

        return transaction(function () use ($request) {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');


            $host = $request->getHost(); // vd: demo.aicrm360.vn
            $subdomain = explode('.', $host)[0]; // lấy phần 'demo'

            $user = User::where('email', $request->email)
                ->where('subdomain', $subdomain)
                ->first();

            if (!$user) {
                return errorResponse("Không tìm thấy tài khoản trên subdomain !", 404, true);
            }

            // ✅ Auth đúng guard
            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                return successResponse("Đăng nhập thành công.", ['redirect' => '/']);
            }

            return errorResponse("Mật khẩu không chính xác!", 400, true);
        });
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->flush();
        return redirect()->route('login');
    }

    public function registerFrom()
    {
        return view('backend.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        return transaction(function () use ($request) {
            $credentials = $request->validated();
            $credentials['code'] ??= $this->generateEmployeeCode();
            $credentials['password'] = bcrypt($credentials['password']);

            User::create($credentials);

            return successResponse("Đăng ký thành công", ['redirect' => '/employees']);
        });
    }

    private function generateEmployeeCode(): string
    {
        $lastCode = User::query()
            ->where('code', 'like', 'NS%')
            ->orderByDesc(DB::raw('CAST(SUBSTRING(code, 3) AS UNSIGNED)'))
            ->value('code');

        if (!$lastCode) {
            return 'NS00001';
        }

        // Lấy phần số phía sau mã
        $number = (int) Str::after($lastCode, 'NS');
        $nextNumber = $number + 1;

        // Luôn pad đến 5 chữ số
        return 'NS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
