<?php

namespace App\Libraries;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

define('SIGN_KEY', env('JWT_SECRET', false));

class AuthenticationHelper
{
    private const TOKEN_SIGN_KEY = SIGN_KEY;

    public static function generateTokensPair(User $user)
    {
        $userID = $user->id;
        $tokenSign = $user->token_signature;
        $accessTokenDuration = env('ACCESS_TOKEN_DURATION', 15) * 60; // Duration is specified in minutes, converted to seconds1
        $refreshTokenDuration = env('REFRESH_TOKEN_DURATION', 360) * 3600; // Refresh token duration is specified in hours, converted to seconds
        $accessTokenPayload = [
            'iat' => time(),
            'expiration' => time() + $accessTokenDuration,
            'user_id' => $userID,
            'type' => 'access',
        ];
        $refreshTokenPayload = [
            'iat' => time(),
            'expiration' => time() + $refreshTokenDuration,
            'user_id' => $userID,
            'signature' => $tokenSign,
            'type' => 'refresh',
        ];
        $accessToken = JWT::encode($accessTokenPayload, self::TOKEN_SIGN_KEY);
        $refreshToken = JWT::encode($refreshTokenPayload, self::TOKEN_SIGN_KEY);

        return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
    }

    public static function generateAccessToken(User $user)
    {
        $userID = $user->id;
        $accessTokenDuration = env('ACCESS_TOKEN_DURATION', 15) * 60; // Duration is specified in minutes, converted to seconds1
        $accessTokenPayload = [
            'iat' => time(),
            'expiration' => time() + $accessTokenDuration,
            'user_id' => $userID,
            'type' => 'access',
        ];
        $accessToken = JWT::encode($accessTokenPayload, self::TOKEN_SIGN_KEY);

        return ['access_token' => $accessToken];
    }

    public static function verifyAccessToken(User $user, $accessToken)
    {
        $decoded = JWT::decode($accessToken, self::TOKEN_SIGN_KEY, ['HS256']);
        $decoded = (array)$decoded;
        if ($decoded['type'] == 'access') {
            if ($decoded['expiration'] >= time()) {
                if ($user->id == $decoded['user_id']) {
                    return true;
                }

                return false;
            }

            throw new UnauthorizedHttpException('', 'Token has expired');
        } else {
            throw new UnauthorizedHttpException('', 'Refresh token provided in authorization header');
        }
    }

    public static function verifyRefreshToken($refreshToken)
    {
        $decoded = JWT::decode($refreshToken, self::TOKEN_SIGN_KEY, ['HS256']);
        $decoded = (array)$decoded;
        if ($decoded['type'] == 'refresh') {
            if ($decoded['expiration'] >= time()) {
                $user = User::findOrFail($decoded['user_id']);
                if ($user->token_signature == $decoded['signature']) {
                    return ['valid' => true, 'user' => $user];
                }

                throw new UnauthorizedHttpException('', 'Token signature is invalid');
            } else {
                throw new UnauthorizedHttpException('', 'Token has expired');
            }
        } else {
            throw new UnauthorizedHttpException('', 'Refresh token provided in authorization header');
        }
    }

    public static function getUserByToken($accessToken)
    {
        $decoded = JWT::decode($accessToken, self::TOKEN_SIGN_KEY, ['HS256']);
        $decoded = (array)$decoded;
        if ($decoded['type'] == 'access') {
            if ($decoded['expiration'] >= time()) {
                return User::findOrFail($decoded['user_id']);
            }

            throw new UnauthorizedHttpException('', 'Token has expired');
        } else {
            throw new UnauthorizedHttpException('', 'Refresh token provided in authorization header');
        }
    }

    public static function destroyAllSessions(User $user)
    {
        $user->token_signature = Str::random(8);
        $user->save();

        return true;
    }
}
