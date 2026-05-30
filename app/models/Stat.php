<?php

namespace Bruder\Heiakim\Model;

use Bruder\Justin;

class Stat extends Justin
{
  /**
   * @var array
   */
  protected $fillable = [
    "mode",
    "tscore",
    "rscore",
    "pp",
    "plays",
    "playtime",
    "acc",
    "max_combo",
    "total_hits",
    "replay_views",
    "xh_count",
    "x_count",
    "sh_count",
    "s_count",
    "a_count",
  ];

  public $timestamps = false;

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "id", "id");
  }
}
