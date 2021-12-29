<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseCode;


class AuthController extends BaseController
{
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'status'            => 'required',
            'position'          => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success = $user;
        $success['token'] = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->where('status','active')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorised.', ['error' => 'The provided credentials are incorrect.'], ResponseCode::HTTP_UNAUTHORIZED);
        }

        $success = $user;
        $success['token'] = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return $this->sendResponse($success, 'User login successfully.');
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'User logout successfully.');
    }

    public function logoutFromAllDevice()
    {
        auth()->user()->tokens()->delete();
        return $this->sendResponse([], 'User logout from all device successfully.');
    }
}
