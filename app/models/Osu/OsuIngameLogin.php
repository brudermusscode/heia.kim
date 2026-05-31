<?php

namespace Heiakim\Model\Osu;

use Heiakim\Justin;
use Heiakim\Model\User;

class OsuIngameLogin extends Justin
{
  /**
   * @var string
   */
  protected $table = "ingame_logins";

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "id", "userid");
  }
}
