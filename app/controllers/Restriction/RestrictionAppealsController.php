<?php

namespace Heiakim\Controller\Restriction;

use Heiakim\Controller\Controller;
use Heiakim\Model\Restriction\Restriction;
use Heiakim\Model\Restriction\RestrictionAppeal;

class RestrictionAppealsController extends Controller
{

  /**
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: [],
      optional: ["content", "live_play_file"]
    );

    $this->authorize();

    # User is not frozen nor restricted?
    if (!CurrentUser->is_restricted() && !CurrentUser->frozen_at)
      return $this->error();

    # Check if the user is restricted and has already sent an
    # appeal after the dynamic waiting period.
    if (CurrentUser->current_appeal_after_restriction_waiting_period())
      return $this->error("<strong>You have submitted an appeal.</strong> We will soon reach out to you!");

    /**
     * @var ?RestrictionAppeal
     */
    $Appeal = CurrentUser->current_appeal();

    # User is not restricted but an appeal for the current
    # freeze already exists and it is not put to status REDO_REQUESTED?
    if ($Appeal && $Appeal->status === "AWAITING_PROCESSING")
      return $this->error("<strong>You have submitted an appeal already.</strong> Wait for it to be processed.");

    # Remove content from params if the user is not yet restricted.
    if (!CurrentUser->is_restricted() && isset($this->params->content)) {
      unset($this->params->content);

      # No live play file is set?
      if (!isset($this->params->live_play_file))
        return error("!FIELDS_MISSING");
    }

    /**
     * @var ?Restriction
     */
    $Restriction = CurrentUser->current_restriction();

    # Append params.
    $this->params->restriction = $Restriction;

    return (new RestrictionAppeal)->new($this->params);
  }

  /**
   * @return object
   */
  public function edit()
  {

    $this->validate_params(
      strict: ["content", "live_play_file"],
      optional: []
    );

    $this->authorize();

    /**
     * @var ?RestrictionAppeal
     */
    $Appeal = CurrentUser->current_appeal();

    if (!$Appeal)
      return $this->error();

    # By now, you should only be able to submit a live play after
    # you have submitted your appeal. If there is a live play
    # already, return an error.
    if ($Appeal->live_play_file)
      return $this->error("<strong>There is a live play attached to your appeal already.</strong> We will soon reach out to you!");

    # Append all.
    $this->params->appeal = $Appeal;

    return $Appeal->edit($this->params);
  }
}
