<?php

namespace App\Providers;

use App\Gelsin\Repositories\Eloquents\UsersRepository;
use App\Gelsin\Repositories\UsersRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // -- Register JWT Lumen load the service provider
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);

        $this->app->bind(UsersRepositoryInterface::class, UsersRepository::class);

    }
}
