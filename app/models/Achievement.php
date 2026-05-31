<?php

namespace Heiakim\Model;

use Heiakim\Justin;

class Achievement extends Justin
{
  /**
   * @var string
   */
  protected $table = "user_achievements";

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "id", "userid");
  }
}
