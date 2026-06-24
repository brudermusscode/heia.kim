<?php

namespace Heiakim\Http;

class Domain
{
  /**
   * All possible localhost domains & IP addresses.
   */
  public static $localhost_domains = [
    "localhost",
    "127.0.0.1",
  ];

  /**
   * Serializes the domain name for localhost and domains with ports.
   *
   * @param string $domain The domain name to serialize
   * @return string The domain name
   */
  public static function clean($domain)
  {
    /**
     * Return the local development domain if it is the dev environment.
     */
    if (current_env() === "dev")
      return _env("DOMAIN");

    $name = preg_replace('/^[^.]+\./', '', $domain);

    if (in_array($name, self::$localhost_domains))
      return "localhost";

    return $name;
  }
}
