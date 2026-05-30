<?php

namespace Bruder\Heiakim\Model\Stat;

use Bruder\Justin;
use Bruder\Heiakim\Model\User;

class StatDevelopment extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "mode",
    "rank",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
