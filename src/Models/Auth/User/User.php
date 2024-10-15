<?php

namespace App\Models\Auth\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Auth\User\Traits\Ables\Rolable;
use App\Models\Auth\User\Traits\Scopes\UserScopes;
use App\Models\Auth\User\Traits\Relations\UserRelations;
use Kyslik\ColumnSortable\Sortable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{

  use HasApiTokens, 
    HasFactory, 
    Notifiable,
    SoftDeletes,
    Sortable,
    UserScopes,
    Rolable,
    UserRelations;


  public $sortable = ['id', 'email', 'name', 'active', 'logined_at', 'created_at'];
  public $incrementing = true;

  /**
   * The database table used by the model.
   *
   * @var string
   */
  // protected $table = 'users';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'email', 'mobile', 'password']; // 
  // 'bank_info', 'bank_owner',

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = ['password', 'remember_token'];

  /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  // protected $dates = ['deleted_at'];

 /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }

  // public function sendEmailVerificationNotification()
  // {
  //   $this->notify(new \App\Notifications\sendEmailVerificationNotification);
  // }

}
