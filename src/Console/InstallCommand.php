<?php

namespace Pondol\Auth\Console;

use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User\User;
use App\Models\Auth\Role\Role;

class InstallCommand extends Command
{
  // use InstallsBladeStack;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  // protected $signature = 'pondol:install-auth';
  protected $signature = 'pondol:install-auth {type=full}'; // full, simple, skip, only

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
      }
    }
    \Artisan::call('migrate');
    $this->info("The pondol's laravel auth installed successfully.");
  }

  private function simpleCase() {
    $this->replaceInFile("'model' => App\Models\User::class,", "'model' => App\Models\Auth\User\User::class,", config_path('auth.php'));
    if(!Schema::hasTable('jobs')) {
      \Artisan::call('queue:table'); // job table  생성 (11 은 php artisan make:queue-table) 명령을 사용하는데 호환성 테스트 필요
    }
  }

  private function replaceInFile($search, $replace, $path)
  {
    file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
  }

  // private function setconfig($key, $data) {
  //   if(is_array($data)) {
  //     foreach($data as $k => $v) {
  //       config()->set('auth.'.$key.'.'.$k, $v);
  //     }
  //   } else {
  //     config()->set('auth.'.$key, $data);
  //   }
    

  //   $text = '<?php return ' . var_export(config('auth'), true) . ';';

  //   // print_r($text);
  //   file_put_contents(config_path('auth.php'), $text);

  //   // \Artisan::call('config:cache'); // 만약 production mode이고 config를 cache 하여 사용하면
  // }

}
