<?php

namespace Pondol\Auth;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Pondol\Auth\Console\CreateCommand;
use Pondol\Auth\Console\InstallCommand;
use Pondol\Auth\Console\VerifySetup;
use Pondol\Auth\Http\Middleware\BypassVerify;
use Pondol\Auth\Http\Middleware\CheckRole;
use Pondol\Auth\Http\Middleware\VerifyEmail;
use Pondol\Auth\Listeners\UserEventSubscriber;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AuthServiceProvider extends ServiceProvider
{
    //  implements DeferrableProvider
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.exi
     *
     * @return void
     */
    // public function boot(\Illuminate\Routing\Router $router)
    public function boot(\Illuminate\Routing\Router $router)
    {

        // Publish config file and merge
        if (! config()->has('pondol-auth')) {
            $this->publishes([
                __DIR__.'/config/pondol-auth.php' => config_path('pondol-auth.php'),
            ], 'config');
        }

        $this->mergeConfigFrom(
            __DIR__.'/config/pondol-auth.php',
            'pondol-auth'
        );

        // Register migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        Event::subscribe(UserEventSubscriber::class);
        Event::listen(
            SocialiteWasCalled::class,
            ['\SocialiteProviders\\Naver\\NaverExtendSocialite', 'handle'],
        );

        Event::listen(
            SocialiteWasCalled::class,
            ['\SocialiteProviders\\Kakao\\KakaoExtendSocialite', 'handle']
        );

        $this->commands([
            InstallCommand::class,
            CreateCommand::class,
            VerifySetup::class,
        ]);

        $this->loadAuthRoutes();

        // 1. 스킨 템플릿 (templates/views, templates/mail) -> resources/views/vendor/pondol-auth/templates
        $this->publishes([
            __DIR__.'/resources/views/templates' => resource_path('views/vendor/pondol-auth/templates'),
        ], 'pondol-auth-templates');

        // 2. 관리자 뷰 및 컴포넌트 전체를 커스텀하고 싶을 때를 위한 태그
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/pondol-auth'),
        ], 'pondol-auth-views');

        // 3. 정적 에셋 (JS, CSS 등)
        $this->publishes([
            __DIR__.'/resources/pondol' => public_path('pondol'),
        ], 'pondol-auth-assets');

        // 4. 태그 없이 artisan vendor:publish 시 한 번에 퍼블리시할 기본 그룹
        $this->publishes([
            __DIR__.'/resources/views/templates' => resource_path('views/vendor/pondol-auth/templates'),
            __DIR__.'/resources/pondol' => public_path('pondol'),
        ], 'pondol-auth');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'pondol-auth');

        $router->aliasMiddleware('role', CheckRole::class);
        $router->aliasMiddleware('bypassverify', BypassVerify::class);

        $router->pushMiddlewareToGroup('web', VerifyEmail::class);
        $router->pushMiddlewareToGroup('admin', 'role:administrator');

        // $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        // $kernel->pushMiddleware(VerifyEmail::class);
    }

    private function loadAuthRoutes()
    {
        $config = config('pondol-auth.route_auth');
        Route::prefix($config['prefix'])
            ->as($config['as'])
            ->middleware($config['middleware'])
            ->namespace('Pondol\Auth\Http\Controllers')
            ->group(__DIR__.'/routes/auth.php');

        $config = config('pondol-auth.route_auth_admin');
        Route::prefix($config['prefix'])
            ->as($config['as'])
            ->middleware($config['middleware'])
            ->namespace('Pondol\Auth\Http\Controllers\Admin')
            ->group(__DIR__.'/routes/auth-admin.php');
    }
}
