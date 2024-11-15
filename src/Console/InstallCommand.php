<?php
namespace Pondol\Auth\Console;

use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
  // use InstallsBladeStack;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pondol:install-auth {type=full}'; // full, simple, only

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Install the auth package and resources';


  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    // $composer = $this->argument('composer');
    $type = $this->argument('type');

    $this->installLaravelAuth($type);
  }

  /**
   * @params String $type full : editor 도 인스톨
   */

  private function installLaravelAuth($type)
  {

    \Artisan::call('vendor:publish',  [
      '--force'=> true,
      '--provider' => 'Pondol\Auth\AuthServiceProvider'
    ]);

    if ($type === 'simple' || $type == 'full') {
      $this->simpleCase();
      if ($type == 'full') {
        $this->call('pondol:install-editor');
        $this->call('pondol:install-common');
      }
    }
    \Artisan::call('migrate');
    $this->info("The pondol's laravel auth installed successfully.");
    $this->comment('To create account, please execute the "php artisan pondol:create-auth" command.');
  }

  private function simpleCase() {
    replaceInFile("'model' => App\Models\User::class,", "'model' => Pondol\Auth\Models\User\User::class,", config_path('auth.php'));
    if(!Schema::hasTable('jobs')) {
      \Artisan::call('queue:table'); // job table  생성 (11 은 php artisan make:queue-table) 명령을 사용하는데 호환성 테스트 필요
    }
  }
}
