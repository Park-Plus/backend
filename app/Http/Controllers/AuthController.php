<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Libraries\AuthenticationHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;

class AuthController extends Controller
{
    public function __construct()
    {

    }
    
    /**
     * Get a new tokens pair
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $mail = $request->email;
        $password = $request->password;
        $user = User::where('email', $mail)->firstOrFail();
        if(Hash::check($password, $user->password)){
            return ["ok" => true, "tokens" => AuthenticationHelper::generateTokensPair($user)];
        }else{
            throw new UnauthorizedException('', 'Authentication failed!');
        }
    }

    /**
     * Get a new access token using a refresh token
     *
     * @param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request){
        if(AuthenticationHelper::verifyRefreshToken(str_replace("Bearer ", "", $request->header('Authorization')))["valid"]){
            return ["ok" => true, "tokens" => AuthenticationHelper::generateAccessToken(AuthenticationHelper::verifyRefreshToken(str_replace("Bearer ", "", $request->header('Authorization')))["user"])];
        }else{
            throw new UnauthorizedException('Refresh token is invalid.');
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return Auth::user();
    }


    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ]);
    }
}
