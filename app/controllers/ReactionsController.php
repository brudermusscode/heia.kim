<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Reaction;

class ReactionsController extends Controller
{

  /**
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
