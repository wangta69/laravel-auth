<?php

namespace Pondol\Auth\Models\User;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laravel\Sanctum\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Pondol\Auth\Models\User\Traits\Ables\Rolable;
use Pondol\Auth\Models\User\Traits\Relations\UserRelations;
use Pondol\Auth\Models\User\Traits\Scopes\UserScopes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    // HasApiTokens,
    use HasFactory,
        Notifiable,
        Rolable,
        SoftDeletes,
        Sortable,
        UserRelations,
        UserScopes;

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
    protected $fillable = ['name', 'email', 'mobile', 'password', 'email_valid', 'is_subscribed']; //
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
        'email_valid' => 'boolean',   // boolean으로 형변환
        'is_subscribed' => 'boolean', // boolean으로 형변환
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
}
