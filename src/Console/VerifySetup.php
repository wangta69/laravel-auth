<?php

namespace Pondol\Auth\Console;

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
        // [수정] 실제 User 모델 클래스 경로 일치: Pondol\Auth\Models\User\User
        $expected = \Pondol\Auth\Models\User\User::class;
        $actual = config('auth.providers.users.model');

        // 상속받은 커스텀 App\Models\User인지 여부도 허용
        $isSubclass = is_subclass_of($actual, $expected);

        if ($actual === $expected || $isSubclass) {
            $this->line('<info>✔ AUTH_MODEL is configured correctly ('.$actual.').</info>');
        } else {
            $this->isSuccessful = false;
            $this->warn('⚠ AUTH_MODEL is not set correctly.');
            $this->comment('Please add or update the following line in your .env file:');
            $this->line("\n    AUTH_MODEL={$expected}\n");
            $this->comment('Or extend '.$expected.' in App\Models\User.');
        }
    }

    protected function checkMiddlewareAlias(Router $router)
    {
        $this->line("\nChecking Middleware Alias...");
        $alias = 'admin';

        $registered = false;
        if (method_exists($router, 'getMiddleware')) {
            $registered = isset($router->getMiddleware()[$alias]);
        }

        // 라라벨 11 이상 커널/라우터 미들웨어 확인 fallback
        if (! $registered && app()->has('router')) {
            $middlewares = app('router')->getMiddleware();
            $registered = isset($middlewares[$alias]);
        }

        if ($registered) {
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
