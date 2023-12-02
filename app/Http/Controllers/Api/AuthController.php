<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Validation\Rule;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'username'  => 'required',
                'password'  => 'required',
            ]);

            if ($validation->fails()) {
                $errors = ValidationHelper::mobile($validation->errors()->all());
                return ResponseHelper::error($errors);
            }

            $user = User::where('email', $request->username)
                    ->orWhere('phone_number', $request->username)
                    ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ResponseHelper::error(
                    message: 'Failed to Login',
                    error: 'Username or password are incorrect', 
                    code: 400,
                );
            }

            $data['user'] = $user;
            $data['token'] = $this->generateNewToken($user);
            return ResponseHelper::success(data: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(message: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }


    public function register(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'full_name' => 'required',
                    'email' => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')],
                    'phone_number' => [
                        'required',
                        'string',
                        'regex:/^(08)[1-9][0-9]{7,11}$/',
                        Rule::unique('users')->whereNull('deleted_at'),
                    ],
                    'password' => 'required|string|min:8',
                ],
            );

            if ($validation->fails()) {
                $errors = ValidationHelper::mobile($validation->errors()->all());
                return ResponseHelper::error(message: 'Failed to Register', error: $errors);
            }

            $user = User::create([
                'full_name'     => $request->full_name,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number,
                'password'      => bcrypt($request->password),
            ]);

            $data['user'] = $user;
            $data['token'] = $this->generateToken($user);

            return ResponseHelper::success(message: 'Registered Successfully', data: $data);
        } catch (QueryException $err) {
            return ResponseHelper::error(message: $err->getMessage());
        } catch (Exception $err) {
            return ResponseHelper::error(
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }
}
