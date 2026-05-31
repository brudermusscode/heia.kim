<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Beatmap;
use Heiakim\Model\User\UserPin;

class UserPinsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

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
      CurrentUser
        ->scores()
        ->find($this->params->reference_id),
      "beatmap" => Beatmap::find($this->params->reference_id),
      default => null,
    };

    # Reference doesn't exist?
    if (!$Reference) return error();

    return (new UserPin)->new($this->params);
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    $this->authorize();

    /**
     * @var ?UserPin
     */
    $Pin = CurrentUser->pins()
      ->where("type", $this->params->type)
      ->where("reference_id", $this->params->id)
      ->first();

    # Delete it!
    $Pin?->delete();

    return success("<strong>Pin removed!</strong>");
  }
}
