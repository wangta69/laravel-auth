<?php
namespace Pondol\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Event;

use Pondol\Auth\Console\InstallCommand;
use Pondol\Auth\Console\CreateCommand;
use Pondol\Auth\Listeners\UserEventSubscriber;
use Pondol\Auth\Http\Middleware\CheckRole;
use Pondol\Auth\Http\Middleware\VerifyEmail;

class AuthServiceProvider extends ServiceProvider { //  implements DeferrableProvider
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
  }

  /**
     * Bootstrap any application services.exi
     *
     * @return void
     */
    //public function boot(\Illuminate\Routing\Router $router)
  public function boot(\Illuminate\Routing\Router $router)
  {

    // Publish config file and merge
    $this->publishes([
      __DIR__ . '/config/pondol-auth.php' => config_path('pondol-auth.php'),
    ], 'config');
    $this->mergeConfigFrom(
      __DIR__ . '/config/pondol-auth.php',
      'pondol-auth'
    );

    // Register migrations
    $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

    Event::subscribe(UserEventSubscriber::class);

    $this->commands([
      InstallCommand::class,
      CreateCommand::class,
    ]);

    $this->loadAuthRoutes();

    $this->publishes([
      __DIR__.'/resources/views/templates' => resource_path('views/auth/templates'),
      __DIR__.'/resources/pondol' => public_path('pondol'),
    ]);

    $this->loadViewsFrom(__DIR__.'/resources/views', 'pondol-auth');

    $router->aliasMiddleware('role', CheckRole::class);
		$router->pushMiddlewareToGroup('admin', 'role:administrator');
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->pushMiddleware(VerifyEmail::class);
  }


  private function loadAuthRoutes()
  {

    $this->loadRoutesFrom(__DIR__ . '/routes/route-url.php');

    $config = config('pondol-auth.route_auth');
    Route::prefix($config['prefix'])
      ->as($config['as'])
      ->middleware($config['middleware'])
      ->namespace('Pondol\Auth\Http\Controllers')
      ->group(__DIR__ . '/routes/auth.php');

    $config = config('pondol-auth.route_auth_admin');
    Route::prefix($config['prefix'])
      ->as($config['as'])
      ->middleware($config['middleware'])
      ->namespace('Pondol\Auth\Http\Controllers\Admin')
      ->group(__DIR__ . '/routes/auth-admin.php');
  }
}
