<?php

/**
 * This class represents a connection to an existing osu! account.
 */

namespace Heiakim\Model;

use Heiakim\Model\Session;
use Heiakim\Model\User;
use Heiakim\Model\Vendor\Osu;
use Heiakim\Utils\Utils;

class ConnectionOsu extends Connection
{

  protected static array $scopes = [
    "identify",
    "public",
  ];

  /**
   * Generates the link to the vendor's API where the user has to auth-
   * orize their account.
   *
   * @return string
   */
  public static function generate_link()
  {

    $credentials = self::oauth_credentials();
    $callback = $credentials["callback"][current_env()]["connect"];
    $return = $credentials["auth_url"]
      . "?client_id=" . $credentials["client_id"]
      . "&redirect_uri=" . $callback
      . "&response_type=code"
      . "&state=" . Utils::random_alpha_token(124)
      . "&scope=" . implode(" ", self::$scopes);

    return $return;
  }

  /**
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {

    /**
     * @var ?User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * Create a new request to the API.
     */
    $Osu = (new Osu)->new($params);

    /**
     * Request failed?
     */
    if (!($Osu instanceof Osu))
      return json_decode($Osu);

    /**
     * User valid?
     */
    if (!isset($Osu->user))
      return request_error("<strong>An error occured while fetching your osu! user information.</strong>", return_json_string: false);

    /**
     * @var null|User|Connect
     */
    $email_in_use =
      User::where("email", $Osu->user->username)->first()
      ?? Connect::where("type", "discord")
      ->where(function ($q) use ($Osu) {
        $q->where("vendor_id", $Osu->user->id)
          ->orWhere("vendor_email", $Osu->user->username);
      })
      ->first();

    /**
     * User exists?
     */
    if ($email_in_use)
      return request_error("<strong>You can't use this osu! Account.</strong> It is already connected to another account.", return_json_string: false);

    /**
     * @var Connect
     */
    $Connect = Connect::create([
      "user_id" => $CurrentUser?->id,
      "type" => "osu",
      "vendor_id" => $Osu->user->id,
      "vendor_email" => $Osu->user->username,
      "access_token" => $Osu->auth->access_token,
      "refresh_token" => $Osu->auth->refresh_token,
      "token_type" => $Osu->auth->token_type,
      "scope" => null,
      "token_id" => null,
      "updated_at" => null,
    ]);

    /**
     * Gets the top 100 of any gamemode.
     */
    $Osu->top_players = $Osu->get_valuable_top_players_of_all_modes();

    /**
     * @var bool
     */
    $is_top_player = $Osu->top_players && in_array($Connect->vendor_id, $Osu->top_players);

    /**
     * Update user or connection to be verified if they are in the top list.
     */
    if ($is_top_player) {
      $CurrentUser->settings->update([
        "is_legit" => 1,
      ]);

      $Connect->update([
        "is_legit" => 1,
      ]);
    }

    return request_success("<strong>Account connected!</strong>", return_json_string: false);
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
    $Osu = $this->fetch_credentials_with_code($params);

    /**
     * Credentials valid?
     */
    if (!($Osu instanceof Osu))
      return $Osu;

    /**
     * @var ?ConnectOsu
     */
    $Connect = ConnectOsu::whereNotNull("user_id")
      ->where("vendor_id", $Osu->user->id)
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
