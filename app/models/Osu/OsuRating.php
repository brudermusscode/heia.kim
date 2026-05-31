<?php

namespace Heiakim\Model\Osu;

use Heiakim\Justin;
use Heiakim\Model\User;

class OsuRating extends Justin
{
  /**
   * @var string
   */
  protected $table = "ratings";

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "id", "userid");
  }
}
