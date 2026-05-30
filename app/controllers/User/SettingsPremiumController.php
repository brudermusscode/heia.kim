<?php

namespace Bruder\Heiakim\Controller\User;

use Bruder\Controller;
use Bruder\Http\Request;
use Bruder\Heiakim\Model\User\UserSettingsPremium;

class SettingsPremiumController extends Controller
{
  /**
   * EDIT
   *
   * @param array $params
   * @return object
   */
  public function edit(array $params)
  {
    $escaped_params = Request::escape_params($params);

    /**
     * User logged & verified?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * User is premium?
     */
    if (!$this->CurrentUser->is_premium())
      return $this->error("!NO_PREMIUM_BUY");

    /**
     * @var UserSettingsPremium
     */
    if (!$this->CurrentUser->premium)
      $Premium = $this->CurrentUser->premium()->create();
    else
      $Premium = $this->CurrentUser->premium;

    /**
     * Append current user.
     */
    $this->params->CurrentUser = $this->CurrentUser;

    return $Premium->edit($escaped_params);
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  public function serialize_params(array $params)
  {
    return $this->serialize_request_params([], $params, ["premium_name_style", "headline"]);
  }
}
