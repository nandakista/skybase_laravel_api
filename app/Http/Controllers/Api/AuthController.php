<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ValidationHelper;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);


            $user = User::where('email', $request->email)->first();
            if (is_null($user)) {
                return ResponseFormatter::error(error: 'User doesn\'t exist', code: 404);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

        
            $token = $user->createToken('session')->plainTextToken;

            $data['user'] = $user;
            $data['token'] = $token;

        return ResponseFormatter::success(data: $data);
        } catch (Exception $err) {
            return ResponseFormatter::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }


    public function register(Request $request)
    {
        $validation = Validator::make(
            request()->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ],
        );

        if ($validation->fails()) {
            $errors = ValidationHelper::errMobile($validation->errors()->all());
            return ResponseFormatter::error(message: 'Failed to update Blog', error: $errors);
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
        ]);

        // $user->assignRole('User');

        $token = $user->createToken('session')->plainTextToken;

        $data['user'] = $user;
        $data['token'] = $token;

        return ResponseFormatter::success(message: 'Registered Successfully', data: $data);
    }
}