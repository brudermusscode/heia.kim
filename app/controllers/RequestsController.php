<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Beatmap;
use Heiakim\Model\BeatmapRequest;

class RequestsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["map_id"],
      optional: [],
    );

    $this->authorize();

    /**
     * @var ?Beatmap
     */
    $Beatmap = Beatmap::findOrReturn($this->params->map_id);

    if (!$Beatmap->ranking_requestable())
      return error("You can't request the ranking of this Beatmap.");

    /**
     * @var ?BeatmapRequest
     */
    $Request = CurrentUser->beatmap_requests()
      ->where("map_id", $Beatmap->id)
      ->where("active", 0)
      ->first();

    if ($Request)
      return error("You have requested a ranking already!");

    $Request = CurrentUser->beatmap_requests()
      ->create([
        "map_id" => $Beatmap->id,
        "datetime" => CURRENT_TIMESTAMP,
        "active" => 1,
      ]);

    return success("Request sent!");
  }
}
