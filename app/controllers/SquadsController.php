<?php

namespace Heiakim\Controller;

use Heiakim\Model\Squad;
use Heiakim\Controller\Controller;

class SquadsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {
    $this->validate_params(
      strict: [],
      optional: ["tag", "name", "joinable", "osu", "taiko", "mania", "ctb"],
    );

    /**
     * User is logged?
     */
    $this->authorize();

    /**
     * Has clan already?
     */
    if (CurrentUser->squad)
      return $this->error("!HAS_SQUAD");

    return (new Squad)->new($this->params);
  }

  /**
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: [],
      optional: ["name", "owner", "leave", "notification", "tag", "joinable", "osu", "taiko", "mania", "ctb", "image_type", "MAX_FILE_SIZE", "files"],
    );

    /**
     * Files for image update sent but no image type?
     */
    if (isset($this->params->files) && !isset($this->params->image_type))
      return $this->error();

    /**
     * SquadUser is authorized?
     */
    authorize(
      resource: CurrentUser?->squad_user,
      can: ["manage", "squad"],
    );

    /**
     * @var Squad
     */
    $Squad = CurrentUser->squad;

    /**
     * Squad exists?
     */
    if (!$Squad)
      return $this->error("<strong>You are not in a squad!</strong> It might have been canceled or you could have been kicked.");

    return $Squad->edit($this->params);
  }
}
