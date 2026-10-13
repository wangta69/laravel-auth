<?php

namespace Pondol\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    protected $signature = 'pondol:install-auth {type=full}'; // full, simple, only

    protected $description = 'Install the auth package and resources';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $type = $this->argument('type');
        $this->installLaravelAuth($type);
    }

    private function installLaravelAuth($type)
    {
        // AuthServiceProvider에 정의된 표준 vendor 뷰 및 에셋 퍼블리시
        $this->call('vendor:publish', [
            '--force' => true,
            '--provider' => 'Pondol\Auth\AuthServiceProvider',
        ]);

        if ($type === 'simple' || $type == 'full') {
            $this->simpleCase();
            if ($type == 'full') {
                if ($this->getApplication()->has('pondol:install-editor')) {
                    $this->call('pondol:install-editor');
                }
                if ($this->getApplication()->has('pondol:install-common')) {
                    $this->call('pondol:install-common');
                }
            }
        }

        $this->call('migrate');
        $this->info("The pondol's laravel auth installed successfully.");
        $this->comment('To create account, please execute the "php artisan pondol:create-auth" command.');
    }

    private function simpleCase()
    {
        // jobs 테이블이 없는 경우 버전 호환(Laravel 10 이하 vs 11 이상) 고려하여 큐 테이블 마이그레이션 생성
        if (! Schema::hasTable('jobs')) {
            try {
                if ($this->getApplication()->has('make:queue-table')) {
                    $this->call('make:queue-table'); // Laravel 11+
                } elseif ($this->getApplication()->has('queue:table')) {
                    $this->call('queue:table');      // Laravel 10 이하
                }
            } catch (\Exception $e) {
                // 이미 파일이 있거나 프레임워크 기본에 포함된 경우 예외 무시
            }
        }
    }
}
