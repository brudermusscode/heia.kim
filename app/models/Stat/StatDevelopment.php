<?php

namespace Heiakim\Model\Stat;

use Heiakim\Justin;
use Heiakim\Model\User;

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
