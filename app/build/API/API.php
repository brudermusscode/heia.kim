<?php

namespace Heiakim\API;

use Heiakim\Trait\ProcessesRequests;

class API
{
  use ProcessesRequests;

  /**
   * The API URL.
   *
   * @var string
   */
  public static $url = "https://heia.kim/api";

  /**
   * Send a request to the API.
   *
   * @param string $model The model to request.
   * @param array $params The parameters to send.
   * @return object JSON object or error message.
   */
  public static function request(string $model, ?array $params = null)
  {
    /**
     * Check if first char of $url is a slash and if not, append
     * it there.
     */
    if (substr($model, 0, 1) != "/")
      $model = "/" . $model;

    $url = self::$url . $model;
    $params = http_build_query($params ?? []);
    $url = $url . "?" . $params;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /**
     * If $params is set, send it as POSTFIELDS.
     */
    // if ($params) {
    //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    // }

    // curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //   "Accept: application/vnd.github.v3+json",
    //   "User-Agent: Awesome-Octocat-App",
    // ]);

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
      return (new self)->error(curl_error($ch));

    curl_close($ch);

    return json_decode($content);
  }
}
