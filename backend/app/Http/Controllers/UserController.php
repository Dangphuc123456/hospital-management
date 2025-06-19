<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailVerification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users|unique:email_verifications',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $token = Str::random(64);

        EmailVerification::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'token' => $token,
            'expires_at' => now()->addHour(),
        ]);

        $verifyUrl = url('/api/verify-email?token=' . $token);

        Mail::html("Click để xác nhận tài khoản: <a href='$verifyUrl'>$verifyUrl</a>", function ($message) use ($request) {
            $message->to($request->email)->subject('Xác thực email');
        });


        return response()->json(['message' => 'Vui lòng kiểm tra email để xác thực.'], 200);
    }
    public function verifyEmail(Request $request)
    {
        $token = $request->query('token');
        $record = EmailVerification::where('token', $token)->first();

        if (!$record) {
            return response()->json(['message' => 'Token không hợp lệ'], 400);
        }

        if (now()->greaterThan($record->expires_at)) {
            $record->delete();
            return response()->json(['message' => 'Token đã hết hạn'], 400);
        }

        // Tạo tài khoản thực
        User::create([
            'name' => $record->name,
            'email' => $record->email,
            'password' => $record->password,
            'role_id' => $record->role_id,
        ]);

        $record->delete();

        return response()->json(['message' => 'Xác thực thành công, tài khoản đã được tạo!']);
    }
    public function login(Request $request)
    {
        // Validate đầu vào
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tìm người dùng theo email + role
        $user = User::where('email', $request->email)
            ->where('role_id', $request->role_id)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email, mật khẩu hoặc vai trò không đúng'
            ], 401);
        }

        // Tạo token đăng nhập (nếu dùng Laravel Sanctum hoặc JWT thì dùng hệ thống của nó)
        $token = bin2hex(random_bytes(32));

        // (Tùy chọn) Lưu token trong user nếu muốn quản lý đăng nhập
        // $user->api_token = $token;
        // $user->save();

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $user
        ]);
    }
    public function user(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $users = User::with('role')->paginate($perPage);

        return response()->json($users);
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->is_active = $request->has('is_active') ? (bool) $request->is_active : $user->is_active;

        $user->save();

        return response()->json(['message' => 'Cập nhật thành công']);
    }

    // Xóa user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Xóa người dùng thành công']);
    }

    // Lấy danh sách vai trò
    public function getRoles()
    {
        $roles = Role::select('id', 'name')->get();
        return response()->json($roles);
    }
}
