<?php

namespace Bruder\Heiakim\Controller\Connect;

use Bruder\Application\Application;
use Bruder\Http\Request;
use Bruder\Controller;
use Bruder\Heiakim\Model\Connect\ConnectDiscord;
use Bruder\Heiakim\Model\Vendor\Discord;
use Bruder\Heiakim\Model\Connect\Connect;
use Bruder\Heiakim\Model\User;

class ConnectUsersController extends Controller
{

  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["vendor_id", "vendor_email", "access_token", "name", "password"], $params, ["avatar_url"]);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * Session valid?
     */
    if ($this->CurrentUser)
      return $this->error("!ALREADY_LOGGED");

    /**
     * @var ?Connect
     */
    $Vendor = Connect::where([
      "vendor_id" => $escaped_params->vendor_id,
      "vendor_email" => $escaped_params->vendor_email,
      "access_token" => $escaped_params->access_token,
    ])
      ->whereNull("user_id")
      ->first();

    /**
     * Vendor credentials exist?
     */
    if (!$Vendor)
      return $this->error();

    /**
     * Append all.
     */
    $escaped_params->vendor = $Vendor;
    $escaped_params->email = $escaped_params->vendor_email;

    return (new User)->new($escaped_params);
  }

  /**
   * @param array $params
   * @return object
   */
  public function auth(array $params)
  {
    $escaped_params = $this->serialize_request_params([], $params, []);

    return (new Discord)->auth($escaped_params);
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params([], $params, []);
  }
}
