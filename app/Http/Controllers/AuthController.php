<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json(['user' => $user,'access_token' => $token]);
    }
    
    /**
     * @throws ValidationException
     */
    public function token(Request $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password')))
        {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        $user = User::where('email', $request['email'])->firstOrFail();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json(['access_token' => $token]);
    }
    /**
     * @desc method for user logout and delete token
     * @return array
     */
    public function logout(): array
    {
        auth()->user()->tokens()->delete();
        
        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
