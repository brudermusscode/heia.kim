<?php

namespace Heiakim\Trait;

use Heiakim\Model\User;

trait DeletableBy
{
  /**
   * @param User $User
   * @return bool
   */
  public function is_deletable_by(User $User)
  {
    return $User->owns($this)
      || (
        $this?->squad
        && $User->squad
        && $User->squad_user
        && $User->squad_user->can_touch($this)
      );
  }
}
