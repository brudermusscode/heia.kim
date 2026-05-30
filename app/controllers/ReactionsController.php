<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Reaction;

class ReactionsController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type", "reference_id", "reaction"],
      optional: [],
    );

    $this->authorize(respect_social_exclusion: true);

    return (new Reaction)->new($this->params);
  }
}
