<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;
    public function __construct()
    {
    }

    public function store(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users'],
                'phone' => ['required'],
                'Job_title' => ['required'],
                'password' => ['required', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ]);
        } else {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'Job_title' => $request['Job_title'],
                'station_id' => $request['station_id'],
                'password' => bcrypt($request['password']),
            ]);
            $station_id = $request['station_id'];
            $user->stations()->attach($station_id);

            return response()->json($user);
        };
    }
}