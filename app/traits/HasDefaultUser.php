<?php

namespace Heiakim\Trait;

use Heiakim\Model\User;

trait HasDefaultUser
{
  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class)

      # Default if the user has been deleted.
      ->withDefault(function ($User) {
        $User->deleted = true;
        $User->id = 0;
        $User->name = "mumei no";
        $User->priv = 3;
        $User->country = "xx";
        $User->donor_end = 0;
        $User->clan_id = 0;
        $User->frozen_at = null;
      });
  }
}
