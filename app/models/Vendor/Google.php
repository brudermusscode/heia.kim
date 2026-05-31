<?php

namespace Heiakim\Model\Vendor;

use Heiakim\Utils\Utils;
use Google\Client;

class Google extends Vendor implements VendorInterface
{

  protected array $scopes = [
    "email",
    "profile"
  ];

  public ?string $access_token = null;

  public ?string $refresh_token = null;

  public ?object $user = null;

  public ?object $auth = null;

  /**
   * @param ?object $auth
   * @return void
   */
  public function __construct(?object $auth = null)
  {
    $this->credentials = $this->oauth_credentials("google");

    if ($auth && isset($auth->access_token)) {
      $this->access_token = $auth->access_token;
      // $this->refresh_token = $auth->refresh_token;
      $this->auth = $auth;
    }
  }

  /**
   * @return Client
   */
  public function get_client()
  {

    /**
     * @var string
     */
    $return_uri = $this->credentials["web"][current_env()]["redirect_uris"]["connect"];

    /**
     * @var Client
     */
    $Client = new Client();
    $Client->setAuthConfig($this->credentials);
    $Client->setScopes($this->scopes);
    $Client->setRedirectUri($return_uri);

    return $Client;
  }

  /**
   * @param object $param
   * @return object
   */
  public function get_auth_uri(object $params)
  {

    /**
     * @var \Google\Client
     */
    $Client = $this->get_client($params->return_uri ?? null);
    $Client->setState(Utils::random_alpha_token(124));

    return request_success(data: $Client->createAuthUrl());
  }

  /**
   * @param object $param
   * @return string|self
   */
  public function new(object $params)
  {

    /**
     * @var Client
     */
    $Client = $this->get_client();
    $Client->addScope($this->scopes);

    /**
     * @var array
     */
    $results = $Client->fetchAccessTokenWithAuthCode($params->code);


    /**
     * Results are valid?
     */
    if (!isset($results["access_token"]))
      return request_error("<strong>Couldn't fetch your credentials.</strong>");

    /**
     * @var self
     */
    $Return = new Self((object) $results);

    /**
     * Server time has to be in sync with the time of the server
     * where the JWT request has been sent. Try the underneath
     * amount of times to create the request.
     */
    \Firebase\JWT\JWT::$leeway = 5;

    do {
      $attempt = 0;

      try {
        $payload = $Client->verifyIdToken($results["id_token"]);
        $retry = false;
        $success = true;
      } catch (\Firebase\JWT\BeforeValidException $e) {
        $attempt++;
        $retry = $attempt < 2;
        $success = false;
      }
    } while ($retry);

    /**
     * Request failed?
     */
    if (!$success)
      return request_error("<strong>An error occured while fetching credentials.</strong>");

    /**
     * Append the vendor user to the self object.
     */
    $Return->user = (object) $payload;

    return $Return;
  }

  /**
   * @param object $param
   * @return object
   */
  public function success(object $params) {}
}
