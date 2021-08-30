<?php

namespace App\Providers;

use App\Libraries\AuthenticationHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Boot the authentication services for the application.
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        Auth::viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                return AuthenticationHelper::getUserByToken(str_replace('Bearer ', '', $request->header('Authorization')));
            }
        });
    }
}
