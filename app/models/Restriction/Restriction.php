<?php

namespace Heiakim\Model\Restriction;

use Heiakim\Application\Application;
use Heiakim\Http\Request;
use Heiakim\Justin;
use Heiakim\Model\User;

class Restriction extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "admin_id",
    "reason",
    "updated_at",
  ];

  public static $appeal_times = [
    ""
  ];

  /**
   * @var User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * @var User
   */
  public function admin()
  {
    return $this->belongsTo(User::class, "admin_id", "id");
  }

  /**
   * @var ?RestrictionAppeal
   */
  public function appeal()
  {
    return $this->belongsTo(RestrictionAppeal::class);
  }
}
