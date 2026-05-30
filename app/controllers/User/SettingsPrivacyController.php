<?php

namespace Bruder\Heiakim\Controller\User;

use Bruder\Controller;
use Bruder\Heiakim\Model\User\UserSettingsPrivacy;

class SettingsPrivacyController extends Controller
{

  /**
   * PUT
   *
   * @return object
   */
  public function update()
  {

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

    /**
     * User is not logged in?
     */
    $this->authorize();

    return $this->CurrentUser
      ->privacy
      ->edit($this->params);
  }
}
