<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\User\UserSettingsPrivacy;

class UserSettingsController extends Controller
{

  /**
   * @return string
   */
  public function update()
  {

    $accepted_params = array_merge(
      [
        "remove_current_profile_picture",
        "image",
        "headline",
        "day",
        "month",
        "year",
        "MAX_FILE_SIZE",
        "checked_notifications_at",
        "mail",
        "accepts_policies",
        "image_history",
        "can_interact",
        "is_public",
      ],
      UserSettingsPrivacy::$mailings
    );

    $this->validate_params(
      strict: [],
      optional: $accepted_params,
    );

    $this->authorize();

    CurrentUser->settings->edit($this->params);

    return success("<strong>Updated!</strong>");
  }
}
