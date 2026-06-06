<?php

/**
 * This class represents any connection to a vendor.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\IsConnectionProvider;
use Heiakim\Http\CURL;

class Connection extends Justin
{
  use IsConnectionProvider;

  /**
   * Represents the vendor's name which is used exactly like this around
   * the whole application. Naming conventions are what make magic poss-
   * ible!
   */
  protected const string PROVIDER = "";

  /**
   * Client credentials like app id & secret.
   */
  protected ?array $credentials = null;

  /**
   * Override the construct method.
   */
  public function __construct()
  {

    # Set credentials to be available easily.
    $this->credentials = oauth_credentials(static::PROVIDER);

    return parent::__construct();
  }

  /**
   * Starts a curl request to a given API endpoint.
   *
   * @param string $api
   * @param array $data
   * @return ?object
   */
  public static function request(string $api, array $data)
  {

    $c = new static()->credentials;

    # Preconfigure the data array with client id and secret.
    $data["client_id"] = $c["client_id"];
    $data["client_secret"] = $c["client_secret"];

    return CURL::start(
      url: $api,
      data: $data,
      options: [
        CURLOPT_HTTPHEADER => $c["authorization_headers"],
      ],
      // debug: true,
    );
  }
}
