<?php

namespace Bruder\Geo;

class Geo
{
  /**
   * @var string
   */
  private $host = "https://www.geoplugin.net/php.gp?ip={IP}&base_currency=EUR&lang=en";

  /**
   * @var string
   */
  private $dev_ip = "91.14.191.205";

  /**
   * Get geo information from geoplugin
   *
   * @param bool $session if user's session should be updated
   * @return object
   */
  public function get()
  {
    return $this->locate();
  }

  /**
   * Will convert the host into a real API request and requests
   * the API of the given host for location data
   *
   * @param ?string $ip the IP to be located
   * @return object The geo information string as an JSON object
   */
  public function locate(?string $ip = null)
  {
    $env = current_env();

    /**
     * Evaluate the actual IP address.
     */
    $ip = match ($env) {
      "dev" => $this->dev_ip,
      default => $ip ?? $this->evaluate_ip_address() ?? $this->dev_ip,
    };

    /**
     * Build the host string with the new IP address.
     */
    $host = str_replace("{IP}", $ip, $this->host);

    /**
     * Return the fetched content.
     */
    return $this->fetch($host);
  }

  /**
   * Builds the fetch query and sets up curl for gathering
   * information from the geo plugin
   *
   * @param string $host
   * @return object The geo information
   */
  public function fetch($host)
  {

    // array(24) {
    //   ["geoplugin_request"]=>
    //   string(13) "91.14.191.205"
    //   ["geoplugin_city"]=>
    //   string(6) "Rheine"
    //   ["geoplugin_region"]=>
    //   string(22) "North Rhine-Westphalia"
    //   ["geoplugin_regionCode"]=>
    //   string(2) "NW"
    //   ["geoplugin_regionName"]=>
    //   string(22) "North Rhine-Westphalia"
    //   ["geoplugin_countryCode"]=>
    //   string(2) "DE"
    //   ["geoplugin_inEU"]=>
    //   int(1)
    //   ["geoplugin_continentCode"]=>
    //   string(2) "EU"
    //   ["geoplugin_continentName"]=>
    //   string(6) "Europe"
    //   ["geoplugin_latitude"]=>
    //   string(7) "52.2674"
    //   ["geoplugin_longitude"]=>
    //   string(6) "7.4551"
    //   ["geoplugin_locationAccuracyRadius"]=>
    //   string(1) "5"
    //   ["geoplugin_timezone"]=>
    //   string(13) "Europe/Berlin"
    //   ["geoplugin_currencyCode"]=>
    //   string(3) "EUR"
    //   ["geoplugin_currencySymbol"]=>
    //   string(7) "€"
    //   ["geoplugin_currencySymbol_UTF8"]=>
    //   string(3) "€"
    //   ["geoplugin_currencyConverter"]=>
    //   string(1) "0"
    // }

    /**
     * @var array
     */
    $empty = [
      "request" => null,
      "hostname" => "None",
      "city" => "None",
      "region" => "None",
      "countryCode" => "xx",
      "loc" => "None",
      "org" => "None",
      "postal" => "000000",
      "continentName" => null,
      "timezone" => "None"
    ];

    // $response = @file_get_contents($host, 'r');

    // /**
    //  * If the request failed, just return empty geo data.
    //  */
    // if (!$response)
    return $empty;

    /**
     * Unserialize the string result.
     *
     * @var array
     */
    $results = unserialize($response);

    /**
     * No country code detected?
     */
    if (empty($results["geoplugin_countryCode"]))
      return $empty;

    /**
     * Remove prefix geoplugin_.
     */
    foreach ($results as $key => $r) {
      $results[str_replace("geoplugin_", "", $key)] = $r;
      unset($results[$key]);
    }

    return $results;
  }

  /**
   * @return string|null
   */
  public function evaluate_ip_address()
  {
    $ip_headers = [
      'HTTP_CLIENT_IP',
      'HTTP_X_FORWARDED_FOR',
      'HTTP_X_FORWARDED',
      'HTTP_X_CLUSTER_CLIENT_IP',
      'HTTP_FORWARDED_FOR',
      'HTTP_FORWARDED',
      'REMOTE_ADDR',
    ];

    foreach ($ip_headers as $header) {
      if (!empty($_SERVER[$header])) {
        $ip_list = explode(',', $_SERVER[$header]);
        foreach ($ip_list as $ip) {
          // Trim and validate each IP address
          $ip = trim($ip);
          if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
          }
        }
      }
    }

    return null; // Return null if no valid IP address is found
  }
}
