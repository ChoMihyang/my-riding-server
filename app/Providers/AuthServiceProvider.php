<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        Passport::enableImplicitGrant();
        Passport::ignoreCsrfToken();

        // 토큰 유효기간
        Passport::tokensExpireIn(now()->addHours(12));

        // refresh token
        Passport::refreshTokensExpireIn(now()->addDays(60));

        // personalAccessToken
        Passport::personalAccessTokensExpireIn(now()->addHours(6));
    }
}
