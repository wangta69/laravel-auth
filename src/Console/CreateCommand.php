<?php

namespace Pondol\Auth\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Pondol\Auth\Models\Role\Role;

class CreateCommand extends Command
{
    protected $signature = 'pondol:create-auth';

    protected $description = 'Create an initial user or administrator';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // [수정] 오타 수정: 'usesr' -> 'user'
        $role = $this->choice('What is your role?', ['administrator', 'user'], 0);
        $name = $this->ask('What is your name?');
        $email = $this->ask('What is your email?');
        $password = $this->secret('What is your password?'); // 비밀번호 노출 방지(secret 권장)

        // 동적 설정된 User 모델 클래스 가져오기
        $userModelClass = config('auth.providers.users.model', \Pondol\Auth\Models\User\User::class);

        $exists = $userModelClass::where('email', $email)->exists();
        if (! $exists) {
            $user = $userModelClass::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $user->email_verified_at = Carbon::now();
            $user->active = 1;
            $user->save();

            // 역할 할당
            if (method_exists($user, 'roles')) {
                $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
            }

            $this->info("User [{$email}] created successfully with [{$role}] role.");
        } else {
            $this->error("The email [{$email}] already exists. Try using another email.");
        }
    }
}
