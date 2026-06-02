<?php

/**
 * This class represents a connection to an existing google account.
 */

namespace Heiakim\Model;

use Heiakim\Model\Session;
use Heiakim\Model\User;
use Heiakim\Model\Vendor\Google;

class ConnectionGoogle extends Connection
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
    $Google = (new Google)->new($params);

    /**
     * Create a new request to the Discord API.
     * {
     *  ["iss"]=> string(27) "https://accounts.google.com"
     *  ["azp"]=> string(72) "946329621106-mvf6n2or1pbnekcrlg3fkgk08veov1ru.apps.googleusercontent.com"
     *  ["aud"]=> string(72) "946329621106-mvf6n2or1pbnekcrlg3fkgk08veov1ru.apps.googleusercontent.com"
     *  ["sub"]=> string(21) "110923898901298953565"
     *  ["email"]=> string(20) "joah.koh24@gmail.com"
     *  ["email_verified"]=> bool(true)
     *  ["at_hash"]=> string(22) "l22ZrAo3Bf_CFY5aZYtV6A"
     *  ["name"]=> string(8) "Joah Koh"
     *  ["picture"]=> string(90) "https://lh3.googleusercontent.com/a/ACg8ocJkH78jhWVksKnt0tcRPflxB3BA2etPPwSvQA8VyCRQ=s96-c"
     *  ["given_name"]=> string(4) "Joah"
     *  ["family_name"]=> string(3) "Koh"
     *  ["locale"]=> string(2) "de"
     *  ["iat"]=> int(1708887008)
     *  ["exp"]=> int(1708890608)
     * }
     */

    /**
     * Credentials valid?
     */
    if (!($Google instanceof Google))
      return json_decode($Google);

    /**
     * Google e-mail is verified?
     */
    if (!$Google->user->email_verified)
      return request_error("<strong>Please verify your Google e-mail first.</strong>", return_json_string: false);

    /**
     * @var null|User|Connect
     */
    $email_in_use = User::where("email", $Google->user->email)->first()
      || Connect::where("type", "discord")
      ->where(function ($q) use ($Google) {
        $q->where("vendor_id", $Google->user->sub)
          ->orWhere("vendor_email", $Google->user->email);
      })
      ->first();

    /**
     * User exists?
     */
    if ($email_in_use)
      return $this->error("<strong>You can't use this Google account.</strong> It is already connected with someone else.");

    /**
     * Create a new connection!
     */
    ConnectGoogle::create([
      "user_id" => $CurrentUser->id,
      "type" => "google",
      "vendor_id" => $Google->user->sub,
      "vendor_email" => $Google->user->email,
      "access_token" => $Google->auth->access_token ?? null,
      "refresh_token" => $Google->auth->refresh_token ?? null,
      "token_type" => $Google->auth->token_type,
      "scope" => $Google->auth->scope,
      "token_id" => $Google->auth->id_token ?? null,
    ]);

    return request_success("<strong>Successfully connected!</strong>", return_json_string: false);
  }

  /**
   * @param object $params
   * @return object
   */
  public function login(object $params)
  {
    /**
     * Create a new request to the API.
     */
    $Google = $this->fetch_credentials_with_code($params);

    /**
     * Credentials valid?
     */
    if (!($Google instanceof Google))
      return $Google;

    /**
     * @var ?ConnectGoogle
     */
    $Connect = ConnectGoogle::whereNotNull("user_id")
      ->where("vendor_id", $Google->user->sub)
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
}
