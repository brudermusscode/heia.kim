<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Feedback;

class FeedbacksController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["action", "type"],
      optional: ["reference_id", "message"],
    );

    $this->authorize();

    $Feedback = (new Feedback)->new($this->params);

    return success(data: ["Feedback" => $Feedback]);
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"],
    );

    $this->authorize();

    $Feedback = CurrentUser->feedback()
      ->where("id", $this->params->id)
      ->first() ?? die(error());

    $Feedback->delete();

    return success(data: ["Feedback" => $Feedback]);
  }
}
