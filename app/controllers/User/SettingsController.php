<?php

namespace Bruder\Heiakim\Controller\User;

use Bruder\Controller;

class SettingsController extends Controller
{

  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    /**
     * Append the image, if it was send with the request.
     */
    if (isset($_FILES["image"]))
      $this->params["files"] = $_FILES["image"];

    $this->validate_params(
      strict: [],
      optional: ["remove_current_profile_picture", "image", "headline", "day", "month", "year", "MAX_FILE_SIZE", "checked_notifications_at"],
    );

    $this->authorize();

    return $this->CurrentUser
      ->settings
      ->edit($this->params);
  }
}
