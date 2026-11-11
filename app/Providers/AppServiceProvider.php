<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::shouldBeStrict(! $this->app->isProduction());

        View::composer([
            'components.layouts.app',
            'components.layouts.vendor',
            'components.layouts.admin',
        ], function ($view) {
            $user = auth()->user();

            $view->with(
                'jumlahNotifikasiBelumDibaca',
                $user ? $user->appNotifications()->belumDibaca()->count() : 0
            );
        });
    }
}
