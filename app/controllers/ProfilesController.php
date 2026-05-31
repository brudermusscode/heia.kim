<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;

class ProfilesController extends Controller
{

  /**
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["profile", "files"],
      optional: [],
    );

    $this->authorize();

    return CurrentUser->profile->edit($this->params);
  }
}
