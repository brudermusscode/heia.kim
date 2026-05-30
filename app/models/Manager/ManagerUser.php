<?php

namespace Bruder\Heiakim\Model\Manager;

use Bruder\Justin;
use Bruder\Heiakim\Model\User;

class ManagerUser extends Justin
{
  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
