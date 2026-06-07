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
    array $headers = [],
    bool $debug = false
  ) {

    # Return null, if there is no url set to curl for.
    if (!$url) return null;

    # If the $data comes in as a string, it can be passed as is. In
    # case of an array, we build the query string first.
    $data = is_array($data) ? static::build_data_query($data) : $data;

    # Start cURL request.
    $curl = curl_init($url);

    # For POST request, append the $data to the postfields.
    if ($type === "POST") {
      $options[CURLOPT_POST] = true;
      $options[CURLOPT_POSTFIELDS] = $data;
    }

    # Always return the response from the request.
    $options[CURLOPT_RETURNTRANSFER] = true;

    # Set a valid user-agent.
    $options[CURLOPT_USERAGENT] = "heia.kim/1.0 (+https://www.heia.kim)";

    # Set verbose output for debug mode.
    // if ($debug)
    $options[CURLOPT_VERBOSE] = false;

    # For development environment without SSL, we need to disable
    # SSL specific validations for cURL requests.
    if (current_env() === "dev") {
      $options[CURLOPT_SSL_VERIFYPEER] = false;
      $options[CURLOPT_SSL_VERIFYHOST] = false;
    }

    # Set headers if any are given.
    if ($headers)
      $options[CURLOPT_HTTPHEADER] = $headers;

    # Set options.
    curl_setopt_array($curl, $options);

    # Execute it!
    $response = curl_exec($curl);

    # Dump error code and message if debug is enabled.
    if ($debug)
      var_dump(curl_errno($curl), curl_error($curl));

    return json_decode($response);
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
