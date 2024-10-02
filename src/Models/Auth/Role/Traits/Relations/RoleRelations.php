<?php

namespace App\Models\Market\Auth\Role\Traits\Relations;

use App\Models\Market\Auth\User\User;

trait RoleRelations
{
    /**
     * Relation with users
     *
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }
}
