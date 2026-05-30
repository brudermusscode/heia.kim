<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Feedback;

class FeedbackController extends Controller
{
  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["action", "type"],
      optional: ["reference_id", "message"],
    );

    /**
     * User logged in?
     */
    $this->authorize();

    return (new Feedback)->new($this->params);
  }
}
