<?php

namespace Heiakim\API;

use Heiakim\Trait\ProcessesRequests;

class Bancho
{
  use ProcessesRequests;

  /**
   * The API URL.
   *
   * @var string
   */
  public $url;

  public function __construct()
  {
    $this->url = _env("API_URL");
  }

  /**
   * Send a request to the API.
   *
   * @param string $model The model to request.
   * @param array $params The parameters to send.
   * @return object JSON object or error message.
   */
  public function request(string $url, ?array $params = null)
  {
    /**
     * Check if first char of $url is a slash and if not, append
     * it there.
     */
    if (substr($url, 0, 1) != "/")
      $url = "/" . $url;

    $url = $this->url . $url;

    if ($params !== null) {
      $params = http_build_query($params ?? []);
      $url = $url . "?" . $params;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /**
     * If the environment is set to development, disable SSL
     */
    if (current_env() === "dev") {
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $content = curl_exec($ch);

    if (curl_errno($ch))
      return $this->error(curl_error($ch));

    curl_close($ch);

    return json_decode($content);
  }
}
