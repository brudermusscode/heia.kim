<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Heiakim\Model\Squad;
use Bruder\Controller;

class SquadsController extends Controller
{

  /**
   * POST
   *
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
    if ($this->CurrentUser->squad)
      return $this->error("!HAS_SQUAD");

    return (new Squad)->new($this->params);
  }

  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    /**
     * Update image
     *
     * Params should always include update_images and image_type.
     * Otherwise it will spit an error. If everything is set,
     * append the FILES superglobal to the escaped params object.
     */
    if (isset($_FILES["image"]))
      $this->params["files"] = $_FILES["image"];

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
      resource: $this->CurrentUser?->squad_user,
      can: ["manage", "squad"],
    );

    /**
     * @var Squad
     */
    $Squad = $this->CurrentUser->squad;

    /**
     * Squad exists?
     */
    if (!$Squad)
      return $this->error("<strong>You are not in a squad!</strong> It might have been canceled or you could have been kicked.");

    return $Squad->edit($this->params);
  }
}
