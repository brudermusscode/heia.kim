<?php

namespace Bruder\Heiakim\Model\Osu;

use Bruder\Justin;
use Bruder\Heiakim\Model\User;

class OsuFavorite extends Justin
{
  /**
   * @var string
   */
  protected $table = "favourites";

  /**
   * @var array
   */
  protected $fillable = [
    "setid",
    "created_at",
  ];

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "id", "userid");
  }
}
