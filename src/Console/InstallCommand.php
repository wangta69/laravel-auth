<?php

namespace Pondol\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Hash;
use App\Models\Auth\User\User;
use App\Models\Auth\Role\Role;
// use Illuminate\Filesystem\Filesystem;
// use Illuminate\Support\Str;
// use Symfony\Component\Process\PhpExecutableFinder;
// use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
  // use InstallsBladeStack;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'pondol:install-auth';

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

    $this->info(" Install Laravel Delivery Tracking ");

    copy(__DIR__.'/../Http/Middleware/CheckRole.php', app_path('Http/Middleware/CheckRole.php'));

     // migration
     (new Filesystem)->copyDirectory(__DIR__.'/../database/migrations', database_path('migrations'));

    $this->replaceInFile("'model' => App\Models\User::class,", "'model' => App\Models\Auth\User\User::class,", config_path('auth.php'));

   
    \Artisan::call('vendor:publish',  [
      '--force'=> true,
      '--provider' => 'Pondol\Auth\AuthServiceProvider'
    ]);
    
    \Artisan::call('migrate');

    $user_name = $this->ask('Name for administrator?'); 
    $user_email = $this->ask('Email for administrator?'); 
    $user_password = $this->ask('Password for administrator?'); 
    
    $count = User::where('email', $user_email)->count();
    if(!$count) {
      $user = User::create([
        'name' => $user_name,
        'email' => $user_email,
        'password' => Hash::make($user_password),
      ]);

      $user->active = 1;
      $user->save();
      $user->roles()->attach(Role::firstOrCreate(['name' => 'administrator']));
    }

   
  }



  private function replaceInFile($search, $replace, $path)
  {
    file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
  }

}
