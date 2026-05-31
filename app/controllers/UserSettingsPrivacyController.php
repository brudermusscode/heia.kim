<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\User\UserSettingsPrivacy;

class UserSettingsPrivacyController extends Controller
{

  /**
   * @return object
   */
  public function update()
  {

    # Merge different params.
    $accepted_params = array_merge(
      [
        "accepts_policies",
        "image_history",
        "can_interact",
        "is_public",
      ],
      (new UserSettingsPrivacy)->mailings
    );

    $this->validate_params(
      strict: [],
      optional: $accepted_params
    );

    $this->authorize();

    return CurrentUser->privacy->edit($this->params);
  }
}
