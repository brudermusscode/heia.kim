<?php

namespace Bruder\Heiakim\Controller\User;

use Bruder\Controller;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\User\UserPin;

class PinsController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    /**
     * User logged in?
     */
    $this->authorize();

    /**
     * Change id to reference_id. So gay!
     */
    $this->params->reference_id = $this->params->id;
    unset($this->params->id);

    /**
     * Validate that the type reference belongs to the user. Only
     * the scores need to be checked on, beatmaps can be pinned by
     * any user.
     */
    $Reference = match ($this->params->type) {
      "score" =>
      $this->CurrentUser
        ->scores()
        ->find($this->params->reference_id),
      "beatmap" => Beatmap::find($this->params->reference_id),
      default => null,
    };

    /**
     * Reference doesn't exist?
     */
    if (!$Reference)
      return $this->error();

    return (new UserPin)->new($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    /**
     * User logged in?
     */
    $this->authorize();

    /**
     * @var ?UserPin
     */
    $Pin =
      $this->CurrentUser
      ->pins()
      ->where("type", $this->params->type)
      ->where("reference_id", $this->params->id)
      ->first();

    /**
     * Delete it!
     */
    $Pin?->delete();

    return $this->success("<strong>Pin removed!</strong>");
  }
}
