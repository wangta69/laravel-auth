<?php
namespace Pondol\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use Pondol\DeliveryTracking\Console\InstallCommand;

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
  }

  /**
     * Bootstrap any application services.exi
     *
     * @return void
     */
    //public function boot(\Illuminate\Routing\Router $router)
  public function boot(\Illuminate\Routing\Router $router)
  {
    $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    $this->loadViewsFrom(__DIR__.'/resources/views', 'auth');

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

    $router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
		$router->pushMiddlewareToGroup('admin', 'role:administrator');
  }
}
