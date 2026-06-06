<?php

namespace Heiakim\Http;

class CURL
{

  /**
   * Starts a cURL request.
   *
   * @param string $url
   * @param string $type
   * @param array $data
   * @param string|array $options
   * @return ?object
   */
  public static function start(
    string $url,
    string $type = "POST",
    string|array $data = [],
    array $options = [],
    bool $debug = false
  ) {

    # Return null, if there is no url set to curl for.
    if (!$url) return null;

    # If the $data comes in as a string, it can be passed as is. In
    # case of an array, we build the query string first.
    $data = is_array($data) ? static::build_data_query($data) : $data;

    # Start cURL request.
    $curl = curl_init($url);

    # Set options.
    curl_setopt_array($curl, $options);

    # For POST request, append the $data to the postfields.
    if ($data && $type === "POST") {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    # Always return the response from the request.
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    # For development environment without SSL, we need to disable
    # SSL specific validations for cURL requests.
    if (current_env() === "dev") {
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $response = curl_exec($curl);
    $response = json_decode($response);

    # Dump error code and message if debug is enabled.
    if ($debug)
      var_dump(curl_errno($curl), curl_error($curl));

    return is_object($response) ? $response : null;
  }

  /**
   * Just builds the data array for the cURL request.
   *
   * @param array $options
   * @return string http query string
   */
  public static function build_data_query(array $options)
  {

    $data = [];

    foreach ($options as $key => $option) {
      $data[$key] = $option;
    }

    return http_build_query($data);
  }
}
