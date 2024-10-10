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
    if(file_exists( app_path('/Listeners/UserEventSubscriber.php')  )) {
      Event::subscribe(\App\Listeners\UserEventSubscriber::class);
    }
  }

  /**
     * Bootstrap any application services.exi
     *
     * @return void
     */
    //public function boot(\Illuminate\Routing\Router $router)
  public function boot(\Illuminate\Routing\Router $router)
  {
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

      __DIR__.'/resources/pondol/auth' => public_path('pondol/auth'),
      // copy config
      __DIR__.'/config/auth-pondol.php' => config_path('auth-pondol.php'),
      // // copy resource 파일
      // __DIR__.'/resources/views/bbs/components' => resource_path('views/bbs/components'),
      // __DIR__.'/resources/views/bbs/templates' => resource_path('views/bbs/templates'),
      __DIR__.'/resources/views/auth' => resource_path('views/auth'),
      __DIR__.'/routes/auth-admin.php' => base_path('/routes/auth-admin.php'),
      __DIR__.'/routes/auth.php' => base_path('/routes/auth.php'),
      // models;
      __DIR__.'/Models/Auth/' => app_path('Models/Auth'),
      // controllers;
      __DIR__.'/Http/Controllers/Auth/' => app_path('Http/Controllers/Auth'),

      // Event and so on;
      // __DIR__.'/Events/' => app_path('Events'),
      // __DIR__.'/Listeners/' => app_path('Listeners'),
      // __DIR__.'/Jobs/' => app_path('Jobs'),

      // __DIR__.'/Mail/' => app_path('Mail'),
      __DIR__.'/Notifications/' => app_path('Notifications')
    ]);


    $router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
		$router->pushMiddlewareToGroup('admin', 'role:administrator');
  }
}
