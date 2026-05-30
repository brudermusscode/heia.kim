<?php

namespace Bruder\Heiakim\Model\Beatmap;

use Bruder\Justin;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\User;

class BeatmapRequest extends Justin
{
  /**
   * @var string
   */
  protected $table = "map_requests";

  /**
   * @var array
   */
  protected $fillable = [
    "id",
    "map_id",
    "player_id",
    "datetime",
    "active",
    "deleted_at",
    "updated_at",
  ];

  public function new(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var Beatmap
     */
    $Beatmap = $params->Beatmap;

    /**
     * Ranking requestable?
     */
    if (!$Beatmap->ranking_requestable())
      return $this->error("<strong>You can't request the ranking of this Beatmap.</strong>");

    /**
     * Either update an extisting request or create a new one.
     */
    $Request = $CurrentUser->beatmap_requests()
      ->where("map_id", $Beatmap->id)
      ->where("active", 0)
      ->first();
    if ($Request)
      $Request->update([
        "datetime" => $this->current_timestamp(),
        "active" => 1,
        "deleted_at" => null,
      ]);
    else {
      $Request = $CurrentUser->beatmap_requests()
        ->create([
          "map_id" => $Beatmap->id,
          "datetime" => $this->current_timestamp(),
          "updated_at" => null,
          "active" => 1,
        ]);
    }

    return $this->success("<strong>Request created!</strong> We will process it soon.");
  }

  /**
   * @var User
   */
  public function user()
  {
    return $this->belongsTo(User::class, "player_id", "id");
  }

  /**
   * @var Beatmap
   */
  public function beatmap()
  {
    return $this->belongsTo(Beatmap::class, "map_id", "id");
  }
}
