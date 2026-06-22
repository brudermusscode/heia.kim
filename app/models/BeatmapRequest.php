<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\Beatmap;
use Heiakim\Model\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeatmapRequest extends Justin
{

  protected $table = "map_requests";

  protected $fillable = [
    "id",
    "map_id",
    "player_id",
    "datetime",
    "active",
    "deleted_at",
    "updated_at",
  ];

  /**
   * @var BelongsTo<User>
   */
  public function user()
  {
    return $this->belongsTo(User::class, "player_id", "id");
  }

  /**
   * @var BelongsTo<Beatmap>
   */
  public function beatmap()
  {
    return $this->belongsTo(Beatmap::class, "map_id", "id");
  }
}
