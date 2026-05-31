<?php

namespace Heiakim\Geo;

use GeoIp2\Exception\GeoIp2Exception;
use Heiakim\Http\Request;

class Geo
{

  private static string $host = "https://api.country.is";

  private static string $dev_ip = "120.0.0.0";

  /**
   * @param string $ip
   * @return ?string
   */
  public static function country_code(?string $ip = null)
  {

    # Prepare the IP address.
    $ip ??= current_env() === "dev" ? self::$dev_ip : Request::get_remote_address();

    if (!filter_var($ip, FILTER_VALIDATE_IP))
      return "xx";

    # No ip address could be detected?
    if (!$ip)
      return "xx";

    # Get data based on the IP.
    $response = @file_get_contents(self::$host . "/" . $ip, 'r');
    $response = json_decode($response ?? "");

    # Response is invalid?
    if (!$response || !is_object($response) || empty($response->country))
      return "xx";

    return strtolower($response->country);
  }
}
