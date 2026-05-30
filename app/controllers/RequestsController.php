<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Beatmap\BeatmapRequest;

class RequestsController extends Controller
{

  /**
   * POST
   *
   * @return object Default return object.
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
    $this->params->Beatmap = Beatmap::findOrReturn($this->params->map_id);

    return (new BeatmapRequest)->new($this->params);
  }
}
