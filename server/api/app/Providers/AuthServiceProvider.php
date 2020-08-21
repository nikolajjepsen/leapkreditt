<?php

namespace App\Providers;

use App\Site;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->header('Authorization') ?? false;
            $siteId = $request->header('Site-Id') ?? false;

            // If Apache, remember following, otherwise Authentication header wont be sent along.
            /* 
            RewriteCond %{HTTP:Authorization} ^(.*)
            RewriteRule .* - [e=HTTP_AUTHORIZATION:%1] 
            */

            if ($siteId && $token) {
                return Site::where('api_token', $token)->where('id', $siteId)->first() ?? null;
            }
        });
    }
}
