<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected function hasAdminAccount(): bool
    {
        return User::where('role', 'admin')->exists();
    }

    public function showLogin()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'teacher'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login', [
            'canPublicRegister' => !$this->hasAdminAccount(),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập trang quản trị.',
            ]);
        }

        return redirect()->route('admin.dashboard');
    }

    public function showRegister()
    {
        $hasAdmin = $this->hasAdminAccount();

        if (!$hasAdmin) {
            return view('admin.auth.register', [
                'bootstrapMode' => true,
                'canSelectRole' => false,
            ]);
        }

        if (!Auth::check()) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Hệ thống đã có tài khoản admin. Chỉ admin hiện tại mới được tạo thêm tài khoản quản trị.');
        }

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Chỉ admin mới có quyền tạo tài khoản quản trị mới.');
        }

        return view('admin.auth.register', [
            'bootstrapMode' => false,
            'canSelectRole' => true,
        ]);
    }

    public function register(Request $request)
    {
        $hasAdmin = $this->hasAdminAccount();

        if ($hasAdmin && (!Auth::check() || !Auth::user()->isAdmin())) {
            abort(403, 'Chỉ admin mới có quyền tạo tài khoản quản trị mới.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        if ($hasAdmin) {
            $rules['role'] = ['required', 'in:admin,teacher'];
        }

        $data = $request->validate($rules);

        $role = $hasAdmin ? $data['role'] : 'admin';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $role,
        ]);

        if (!$hasAdmin) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Tạo tài khoản admin đầu tiên thành công.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Tạo tài khoản quản trị thành công.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
}
