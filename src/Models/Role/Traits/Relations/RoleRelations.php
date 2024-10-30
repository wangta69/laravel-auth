<?php

namespace Pondol\Auth\Models\Role\Traits\Relations;

use Pondol\Auth\Models\User\User;

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
