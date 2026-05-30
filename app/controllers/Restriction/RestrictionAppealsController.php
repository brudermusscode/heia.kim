<?php

namespace Bruder\Heiakim\Controller\Restriction;

use Bruder\Application\Application;
use Bruder\Controller;
use Bruder\Http\Request;
use Bruder\Heiakim\Model\Restriction\Restriction;
use Bruder\Heiakim\Model\Restriction\RestrictionAppeal;

class RestrictionAppealsController extends Controller
{
  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params([], $params, ["content", "live_play_file"]);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * User logged?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * User is not frozen nor restricted?
     */
    if (!$this->CurrentUser->is_restricted() && !$this->CurrentUser->frozen_at)
      return $this->error();

    /**
     * Check if the user is restricted and has already sent an
     * appeal after the dynamic waiting period.
     */
    if ($this->CurrentUser->current_appeal_after_restriction_waiting_period())
      return $this->error("<strong>You have submitted an appeal.</strong> We will soon reach out to you!");

    /**
     * @var ?RestrictionAppeal
     */
    $Appeal = $this->CurrentUser
      ->current_appeal();

    /**
     * User is not restricted but an appeal for the current
     * freeze already exists and it is not put to status REDO_REQUESTED?
     */
    if ($Appeal && $Appeal->status === "AWAITING_PROCESSING")
      return $this->error("<strong>You have submitted an appeal already.</strong> Wait for it to be processed.");

    /**
     * Remove content from params if the user is not yet restricted.
     */
    if (!$this->CurrentUser->is_restricted() && isset($escaped_params->content)) {
      unset($escaped_params->content);

      /**
       * No live play file is set?
       */
      if (!isset($escaped_params->live_play_file))
        return $this->error("!FIELDS_MISSING");
    }

    /**
     * @var ?Restriction
     */
    $Restriction = $this->CurrentUser
      ->current_restriction();

    /**
     * Append all.
     */
    $this->params->CurrentUser = $this->CurrentUser;
    $escaped_params->restriction = $Restriction;

    return (new RestrictionAppeal)->new($escaped_params);
  }

  /**
   * UPDATE
   *
   * @param array $params
   * @return object
   */
  public function edit(array $params)
  {
    $escaped_params = $this->serialize_request_params(["live_play_file", "content"], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * User logged?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * @var ?RestrictionAppeal
     */
    $Appeal = $this->CurrentUser->current_appeal();

    /**
     * User has a current appeal?
     */
    if (!$Appeal)
      return $this->error();

    /**
     * By now, you should only be able to submit a live play after
     * you have submitted your appeal. If there is a live play
     * already, return an error.
     */
    if ($Appeal->live_play_file)
      return $this->error("<strong>There is a live play attached to your appeal already.</strong> We will soon reach out to you!");

    /**
     * Append all.
     */
    $this->params->CurrentUser = $this->CurrentUser;
    $escaped_params->appeal = $Appeal;

    return $Appeal->edit($escaped_params);
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params([], $params, []);
  }
}
