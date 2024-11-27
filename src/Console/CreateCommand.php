<?php

namespace Pondol\Auth\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Pondol\Auth\Models\User\User;
use Pondol\Auth\Models\Role\Role;
use Carbon\Carbon;

class CreateCommand extends Command
{
  // use InstallsBladeStack;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  // protected $signature = 'pondol:install-auth';
  protected $signature = 'pondol:create-auth'; 

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
    $role = $this->choice('What is your role?', ['administrator', 'usesr'], 0);
    $name = $this->ask('What is your name?'); 
    $email = $this->ask('What is your email?'); 
    $password = $this->ask('What is your password?'); 
    
    $count = User::where('email', $email)->count();
    if(!$count) {
      $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
      ]);
      $user->email_verified_at = Carbon::now();
      $user->active = 1;
      $user->save();
      $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
      $this->info('a user created successfully.'); 
    } else {
      $this->comment($email.' already exists. try using another email'); 
    }
    return;
  }
}
