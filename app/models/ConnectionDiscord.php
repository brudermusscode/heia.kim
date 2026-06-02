<?php

/**
 * This class represents a connection to an existing Discord account.
 */

namespace Heiakim\Model;

use Heiakim\Model\Session;
use Heiakim\Model\User;
use Heiakim\Model\Vendor\Discord;

class ConnectionDiscord extends Connection
{

  /**
   * @param object $params
   * @return string
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * Create a new request to the API.
     */
    $Discord = (new Discord)->new($params);

    /**
     * Request failed?
     */
    if (!($Discord instanceof Discord))
      return json_decode($Discord);

    /**
     * @var null|User|Connect
     */
    $email_in_use =
      User::where("email", $Discord->user->email)->first()
      ?? Connect::where("type", "discord")
      ->where(function ($q) use ($Discord) {
        $q->where("vendor_id", $Discord->user->id)
          ->orWhere("vendor_email", $Discord->user->email);
      })
      ->first();

    /**
     * User exists?
     */
    if ($email_in_use)
      return $this->error("<strong>You can't use this Discord.</strong> It is already connected to another account.", return_json_string: false);

    /**
     * Join the user to our
     */
    $Discord->join_server();

    /**
     * Create the connection!
     */
    ConnectDiscord::create([
      "user_id" => $CurrentUser->id,
      "type" => "discord",
      "vendor_id" => $Discord->user->id,
      "vendor_email" => $Discord->user->email,
      "access_token" => $Discord->access_token,
      "refresh_token" => $Discord->refresh_token,
      "token_type" => $Discord->auth->token_type,
      "scope" => $Discord->auth->scope,
    ]);

    return $this->success("<strong>Account connected!</strong>", return_json_string: false);
  }

  /**
   * @param object $params
   * @return string
   */
  public function login(object $params)
  {
    /**
     * Create a new request to the API.
     */
    $Discord = $this->fetch_credentials_with_code($params);

    /**
     * Credentials valid?
     */
    if (!($Discord instanceof Discord))
      return $Discord;

    /**
     * @var ?ConnectDiscord
     */
    $Connect = ConnectDiscord::whereNotNull("user_id")
      ->where("vendor_id", $Discord->user->id)
      ->first();

    /**
     * Account is signed up already with this vendor?
     */
    if (!$Connect)
      return $this->error("<strong>Couldn't find this account.</strong>");

    /**
     * Create a session!
     */
    return (new Session)->new((object) [
      "user_id" => $Connect->user_id,
    ]);
  }

  /**
   * @param int $role_id
   * @return bool
   */
  public function give_role(int $role_id)
  {
    return (new Discord)->give_role($role_id, $this->vendor_id);
  }

  /**
   * @param int $role_id
   * @return bool
   */
  public function take_role(int $role_id)
  {
    return (new Discord)->take_role($role_id, $this->vendor_id);
  }
}
