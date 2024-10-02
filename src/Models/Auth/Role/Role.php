<?php

namespace App\Models\Market\Auth\Role;

use Illuminate\Database\Eloquent\Model;
use App\Models\Market\Auth\Role\Traits\Scopes\RoleScopes;
use App\Models\Market\Auth\Role\Traits\Relations\RoleRelations;

class Role extends Model
{
    use RoleScopes,
        RoleRelations;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

}
