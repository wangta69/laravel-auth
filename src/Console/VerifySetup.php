<?php

namespace Pondol\Auth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;

class VerifySetup extends Command
{
    protected $signature = 'pondol-auth:verify';
    protected $description = 'Verify that the Pondol Auth package is set up correctly.';

    protected bool $isSuccessful = true;

    public function handle(Router $router)
    {
        $this->info('Verifying Pondol Auth setup...');

        $this->checkAuthModel();
        $this->checkMiddlewareAlias($router);

        if ($this->isSuccessful) {
            $this->info("\n✅ All checks passed! Pondol Auth is set up correctly.");
        } else {
            $this->error("\n❌ Some setup steps are missing. Please follow the instructions above.");
        }

        return $this->isSuccessful ? 0 : 1;
    }

    protected function checkAuthModel()
    {
        $this->line("\nChecking AUTH_MODEL...");
        $expected = 'Pondol\Auth\Models\User';
        $actual = config('auth.providers.users.model');

        if ($actual === $expected) {
            $this->line('<info>✔ AUTH_MODEL is configured correctly.</info>');
        } else {
            $this->isSuccessful = false;
            $this->warn('⚠ AUTH_MODEL is not set correctly.');
            $this->comment('Please add or update the following line in your .env file:');
            $this->line("\n    AUTH_MODEL={$expected}\n");
            $this->comment('Then, clear your config cache: php artisan config:cache');
        }
    }

    protected function checkMiddlewareAlias(Router $router)
    {
        $this->line("\nChecking Middleware Alias...");
        $alias = 'admin';
        
        if ($router->getMiddleware()[$alias] ?? null) {
            $this->line('<info>✔ \'admin\' middleware alias is registered.</info>');
        } else {
            $this->isSuccessful = false;
            $this->warn('⚠ \'admin\' middleware alias is not registered.');
            $this->comment('Please add the following to your bootstrap/app.php file inside the withMiddleware() method:');
            
            $code = "
    ->withMiddleware(function (Middleware \$middleware) {
        \$middleware->alias([
            'admin' => \Pondol\Auth\Http\Middleware\CheckRole::class,
        ]);
    })";

            $this->line($code);
        }
    }
}