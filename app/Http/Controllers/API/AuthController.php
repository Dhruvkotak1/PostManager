<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        //here we user validator method for more functionality
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => 'Validation error',
                    'errors' => $validateUser->errors()->all()
                ],
                401
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json(
            [
                'status' => true,
                'msg' => 'User Created Successfully',
                'user' => $user
            ],
            200
        );
    }

    public function login(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

        if ($validateUser->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => 'Authentication failed',
                    'errors' => $validateUser->errors()->all()
                ],
                404
            );
        }

       

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $authUser = Auth::user();
            return response()->json(
                [
                    'status' => true,
                    'msg' => 'User logged in Successfully',
                    //Here we creating a token of random number whose value is saved in "API Token" key.The error is not an actual error
                    'token' => $authUser->createToken("API Token")->plainTextToken,
                    'token_type' => 'bearer',
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'msg' => 'Email & Password does not matched',

                ],
                404
            );
        }
    }

    public function logout(Request $request) {
        $user = $request->user();
        $user->tokens()->delete(); //Deleting tokens of user during logout

        return response()->json(
            [
                'status' => true,
                'msg' => 'You logged out successfully',
                'user' => $user
            ],
            200
        );
    }
}
