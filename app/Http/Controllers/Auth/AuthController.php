<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use TheSeer\Tokenizer\Token;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {

        // تسجيل الدخول
        // $credentials = $request->only('email', 'password');
        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {

        $credentialsEmail = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        $credentialsPhone = [
            'phone' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        if (Auth::attempt($credentialsEmail) || Auth::attempt($credentialsPhone)) {
            // إنشاء رمز مميز
            /** @var User */
            $user = Auth::User();
            $token = $user->createToken('API Token')->plainTextToken;
            // إرسال رد
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'error' => 'Error in email or password.'
            ], 401);
        }
    }
    public function logout(Request $request)
    {
        // $tokenId = intval(explode("|", request()->bearerToken())[0]);
        // $user = $request->user();
        // $request->session()->invalidate();
        // $user->tokens()->where('id', $tokenId)->delete();
        // return response()->json([$tokenId, $user]);

        // تأكد من تسجيل دخول المستخدم
        // if (!Auth::check()) {
        //     return response()->json('Unauthorized', 401);
        // }
        $request->user()->currentAccessToken()->delete();
        return response()->json('Logged out successfully', 200);
    }
}