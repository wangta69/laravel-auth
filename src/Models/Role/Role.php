<?php

namespace Pondol\Auth\Models\Role;

use Illuminate\Database\Eloquent\Model;
use Pondol\Auth\Models\Role\Traits\Scopes\RoleScopes;
use Pondol\Auth\Models\Role\Traits\Relations\RoleRelations;

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
