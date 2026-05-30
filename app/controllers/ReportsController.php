<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Report;

class ReportsController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["reference_id", "report_type", "comment_string"],
      optional: ["user_notification"],
    );

    /**
     * User logged in?
     */
    $this->authorize();

    /**
     * Append user notification if not set.
     */
    if (!isset($this->params->user_notification))
      $this->params->user_notification = 0;

    return (new Report)->new($this->params);
  }
}
