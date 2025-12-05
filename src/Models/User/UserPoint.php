<?php

namespace Pondol\Auth\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    const UPDATED_AT = null; // created_at만 사용

    protected $fillable = [
        'user_id',
        'point',
        'cur_sum',
        'item',
        'sub_item',
        'rel_item',
        'is_paid',
        'expires_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
