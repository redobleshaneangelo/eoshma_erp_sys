<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Registration Error',
                    'html' => implode('<br>', $validator->errors()->all())
                ], Response::HTTP_UNPROCESSABLE_ENTITY
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            if($user) {
                return response()->json([
                    'message' => [
                        'icon' => 'success',
                        'title' => 'Success',
                        'text' => 'User created successfully'
                    ], Response::HTTP_OK
                ]);
            }
        }
    }
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Login Error',
                    'html' => implode('<br>', $validator->errors()->all())
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Invalid Credentials'
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $user = Auth::user();
        $user->load('department');
        $token = $user->createToken('auth_token', [ $request->ip() ])->plainTextToken;
        $user->hashed_id = Hashids::encode($user->id);

        return response()->json([
            'user' => [
                'id' => $user->hashed_id,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'image' => $user->image,
                'role' => $user->role,
                'dept_name' => $user->department?->name,
            ],
            'role' => $user->getRoleNames()->first(),
            'permissions' => $user->getAllPermissions()
                ->pluck('name'),
            'token' => $token,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        
        return response()->json([
            'message' => [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'User logged out successfully'
            ],
        ], Response::HTTP_OK);
    }
}
