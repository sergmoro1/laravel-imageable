<?php

namespace Sergmoro1\Imageable;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Sergmoro1\Imageable\View\Components\Upload;
use Sergmoro1\Imageable\Http\Middleware\AuthenticateOnceWithBasicAuth;

class ImageableServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'imageable');
    }

    public function boot()
    {
        $this->registerRoutes();

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('auth.basic.once', AuthenticateOnceWithBasicAuth::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
              __DIR__.'/../resources/views' => resource_path('views/vendor/imageable'),
            ], 'views');
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'imageable');
        $this->loadViewComponentsAs('imageable', [
            Upload::class,
        ]);
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }
    
    protected function routeConfiguration()
    {
        return [
            'prefix' => config('imageable.prefix'),
        ];
    }    
}