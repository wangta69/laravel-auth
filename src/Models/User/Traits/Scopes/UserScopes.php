<?php

namespace Pondol\Auth\Models\User\Traits\Scopes;

use Arcanedev\Support\Bases\Model;

trait UserScopes
{
    /**
     * Fetch users by a given role id or role name value.
     *
     * @param $query \Illuminate\Database\Eloquent\Builder
     * @param $role
     * @return mixed
     */
    public function scopeWhereRole($query, $role)
    {
        if ($role instanceof Model) $role = $role->getKey();

        $query->whereHas('roles', function ($query) use ($role) {
            /** @var $query \Illuminate\Database\Query\Builder */
            $query->orWhere('id', $role)->orWhere('name', $role);
        });

        return $query;
    }

    // public function scopeWhereSaleser($query, $role)
    // {
    //     if ($role instanceof Model) $role = $role->getKey();

    //     $query->whereHas('roles', function ($query) use ($role) {
    //         /** @var $query \Illuminate\Database\Query\Builder */
    //         $query->Where('name', $role);
    //     });

    //     return $query;
    // }
/*
    public function scopeCurSum($query)
    {
        //print_r($query);
        $query->points();//->select(['cur_sum']);
        return $query;
    }
    */
}
