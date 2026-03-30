<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(Request $request)
    {
        // Lưu trạng thái "ghi nhớ đăng nhập" vào session trước khi redirect sang Google
        $request->session()->put('socialite_remember', $request->boolean('remember'));

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Đã có tài khoản với email này, cập nhật google_id
                $user->update([
                    'google_id' => $googleUser->id,
                    'last_login_at' => now(),
                    'avatar_url' => $googleUser->avatar,
                ]);
            } else {
                // Chưa có tài khoản, tạo mới
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => 'user',
                    'status' => 'active',
                    'last_login_at' => now(),
                    'avatar_url' => $googleUser->avatar,
                    // password được phép null do migration
                ]);
            }

            // Đọc trạng thái "ghi nhớ đăng nhập" từ session
            $remember = session()->pull('socialite_remember', false);
            Auth::login($user, $remember);

            return redirect()->intended(url('/'));
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Đã có lỗi xảy ra khi đăng nhập bằng Google: ' . $e->getMessage()]);
        }
    }
}
