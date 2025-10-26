<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {   
        //Passport::ignoreRoutes();
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addDays(7));
        Passport::refreshTokensExpireIn(now()->addDays(14));

        Passport::tokensCan([
            'user'  => 'Access basic user features',
            'admin' => 'Full access to all resources',
        ]);
    }
}
