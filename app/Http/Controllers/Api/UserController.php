<?php

namespace App\Http\Controllers\Api;


use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\StorageHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile()
    {
        try {
            $session = auth('sanctum')->user();
            if ($session) {
                $user = User::find(auth('sanctum')->user()->id);
                return ResponseHelper::success(message: 'Get user successfully', data: $user->getData());
            } else {
                return ResponseHelper::unauthorized();
            }
        } catch (Exception $err) {
            return ResponseHelper::error(
                message: 'Failed to get profile',
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string',
                'phone_number' => [
                    'required',
                    'string',
                    'regex:/^(\+628|628|08)[1-9][0-9]{7,11}$/',
                    'unique:users,phone_number,' . $user->id,
                ],
                'email' => 'string|email|unique:users,email,' . $user->id,
                'avatar' => 'nullable|image|max:2048',
            ], [
                'avatar.uploaded' => 'Maximum file size to upload is 2MB'
            ]);

            if ($validator->fails()) {
                $errors = ValidationHelper::mobile($validator->errors()->all());
                return ResponseHelper::error($errors);
            }

            if ($request->avatar != null) {
                $profilePhotoPath = StorageHelper::updateFile(
                    config('constant.profiles_path'),
                    $user->avatar,
                    $request->file('avatar'),
                );
            }

            $user->update([
                'full_name' => $request->full_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'avatar' => $profilePhotoPath ?? $user->avatar,
            ]);

            return ResponseHelper::success(message: 'Update Profile Successfully', data: $user->getData());
        } catch (Exception $err) {
            return ResponseHelper::error(
                message: 'Failed to Update',
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $user = User::find($user->id);

            $validator = Validator::make($request->all(), [
                'current_password' => [
                    'required',
                    function ($attribute, $value, $fail) use ($user) {
                        if (!Hash::check($value, $user->password)) {
                            $fail('Your current password is wrong.');
                        }
                    },
                ],
                'new_password' => 'required|min:8|different:current_password',
                'confirm_password' => 'required|same:new_password',
            ]);

            if ($validator->fails()) {
                $errors = ValidationHelper::mobile($validator->errors()->all());
                return ResponseHelper::error($errors);
            }

            $user->update(['password' => bcrypt($request->new_password)]);
            $this->clearToken($user);
            return ResponseHelper::success(
                message: 'Change Password Successfully', 
                data: $user->getData(),
            );
        } catch (Exception $err) {
            return ResponseHelper::error(
                message: 'Failed to Change',
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $user->delete();
            $this->clearToken($user);
            return ResponseHelper::success(message: 'Delete Account Successfully', data: $user->getData());
        } catch (Exception $err) {
            return ResponseHelper::error(
                message: 'Failed to delete account',
                error: json_decode($err->getMessage()) ?? $err->getMessage(),
                code: 500,
            );
        }
    }
}
