<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

  protected $attributes = [
    "tscore" => 0,
    "rscore" => 0,
    "pp" => 0,
    "plays" => 0,
    "playtime" => 0,
    "max_combo" => 0,
    "total_hits" => 0,
    "replay_views" => 0,
    "xh_count" => 0,
    "x_count" => 0,
    "sh_count" => 0,
    "s_count" => 0,
    "a_count" => 0,
    "acc" => 0.0,
  ];

  public $timestamps = false;

  /**
   * @return BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class, "id", "id");
  }
}
