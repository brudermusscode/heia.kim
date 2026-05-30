<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;

class ProfilesController extends Controller
{

  /**
   * PUT
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
      strict: ["profile", "files"],
      optional: [],
    );

    /**
     * User logged in?
     */
    $this->authorize();

    return $this->CurrentUser
      ->profile
      ->edit($this->params);
  }
}
