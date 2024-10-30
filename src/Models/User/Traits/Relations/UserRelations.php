<?php

namespace Pondol\Auth\Models\User\Traits\Relations;

use Pondol\Auth\Models\Role\Role;
use Pondol\Auth\Models\User\SocialAccount;
// use App\Models\Protection\ProtectionShopToken;
// use App\Models\Protection\ProtectionValidation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait UserRelations
{
  /**
   * Relation with role
   *
   * @return BelongsToMany
   */
  public function roles()
  {
      return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
  }

  /**
   * Relation with social provider
   *
   * @return HasMany
   */
  public function providers()
  {
    return $this->hasMany(SocialAccount::class);
  }

  public function points()
  {
    return $this->hasMany('Pondol\Auth\Models\User\UserPoint');
  }

  public function curPoint()
  {
    $points = $this->points()->select('cur_sum')->orderBy('id', 'desc')->first();
    return $points ? $points->cur_sum : 0;
  }

}
