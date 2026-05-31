<?php

namespace Heiakim\Model\Manager;

use Heiakim\Justin;
use Heiakim\Model\User;

class ManagerAuthentication extends Justin
{
  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
