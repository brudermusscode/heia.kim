<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;

class UserSettingsController extends Controller
{

  /**
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: [],
      optional: ["remove_current_profile_picture", "image", "headline", "day", "month", "year", "MAX_FILE_SIZE", "checked_notifications_at"],
    );

    $this->authorize();

    return CurrentUser->settings->edit($this->params);
  }
}
