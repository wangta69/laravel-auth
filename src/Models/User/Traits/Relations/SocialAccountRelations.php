<?php

namespace Pondol\Auth\Models\User\Traits\Relations;

use Pondol\Auth\Models\Role\Role;
use Pondol\Auth\Models\User\SocialAccount;
use Pondol\Auth\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait SocialAccountRelations
{
  /**
   * @return BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
