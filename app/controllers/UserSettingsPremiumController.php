<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Http\Request;
use Heiakim\Model\User\UserSettingsPremium;

class UserSettingsPremiumController extends Controller
{

  /**
   * @param array $params
   * @return object
   */
  public function edit(array $params)
  {

    $escaped_params = Request::escape_params($params);

    /**
     * User logged & verified?
     */
    if (!CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * User is premium?
     */
    if (!CurrentUser->is_premium())
      return $this->error("!NO_PREMIUM_BUY");

    /**
     * @var UserSettingsPremium
     */
    if (!CurrentUser->premium)
      $Premium = CurrentUser->premium()->create();
    else
      $Premium = CurrentUser->premium;

    /**
     * Append current user.
     */
    $this->params->CurrentUser = CurrentUser;

    return $Premium->edit($escaped_params);
  }
}
