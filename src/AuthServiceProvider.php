<?php
namespace Pondol\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;

use Pondol\Auth\Console\InstallCommand;
// use App\Listeners\UserEventSubscriber;

class AuthServiceProvider extends ServiceProvider { //  implements DeferrableProvider
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    if ($this->app->runningInConsole()) {
      $this->commands([
        InstallCommand::class,
      ]);
    }
    // \Log::info('AuthServiceProvider register 1');
    // if(file_exists( app_path('/Listeners/UserEventSubscriber.php')  )) {
    //   Event::subscribe(\App\Listeners\UserEventSubscriber::class);
    //   \Log::info('AuthServiceProvider register 2');
    // }
  }

  /**
     * Bootstrap any application services.exi
     *
     * @return void
     */
    //public function boot(\Illuminate\Routing\Router $router)
  public function boot(\Illuminate\Routing\Router $router)
  {

    
    if(file_exists( app_path('/Listeners/UserEventSubscriber.php')  )) {
      \Log::info('AuthServiceProvider boot1111 =========================');
      Event::subscribe(\App\Listeners\UserEventSubscriber::class);
    }

    // $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    // $this->loadViewsFrom(__DIR__.'/resources/views', 'auth');

    if(file_exists( base_path('/routes/auth-admin.php')  )) {
      Route::middleware(['web'])->group(function () {
        $this->loadRoutesFrom(base_path('/routes/auth-admin.php'));
      });
    }
    if(file_exists( base_path('/routes/auth.php')  )) {
      Route::middleware(['web'])->group(function () {
        $this->loadRoutesFrom(base_path('/routes/auth.php'));
      });
    }


    $this->publishes([
      __DIR__.'/resources/pondol/auth/route.js' => resource_path('pondol/route.js'),
      __DIR__.'/resources/pondol/auth/' => public_path('pondol/auth'),
      // copy config
      __DIR__.'/config/auth-pondol.php' => config_path('auth-pondol.php'),
      // // copy resource 파일
      // __DIR__.'/resources/views/bbs/components' => resource_path('views/bbs/components'),
      // __DIR__.'/resources/views/bbs/templates' => resource_path('views/bbs/templates'),
      __DIR__.'/resources/views/auth' => resource_path('views/auth'),
      __DIR__.'/routes/' => base_path('/routes'),
      // models;
      __DIR__.'/Models/Auth/' => app_path('Models/Auth'),
      // controllers;
      __DIR__.'/Http/Controllers/Auth/' => app_path('Http/Controllers/Auth'),
      __DIR__.'/Http/Middleware/' => app_path('Http/Middleware'),
      __DIR__.'/Traits/' => app_path('Traits'),
      __DIR__.'/database/migrations/' => database_path('migrations'),
      __DIR__.'/Notifications/' => app_path('Notifications'),
      __DIR__.'/Listeners/' => app_path('Listeners')
    ]);

    $router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
		$router->pushMiddlewareToGroup('admin', 'role:administrator');
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->pushMiddleware(\App\Http\Middleware\VerifyEmail::class);
  }
}
