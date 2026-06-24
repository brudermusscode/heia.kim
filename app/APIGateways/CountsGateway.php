<?php

namespace Heiakim\APIGateway\Count;

use Heiakim\Gateway;
use Heiakim\Http\Request;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;
use Heiakim\Model\Squad;
use Heiakim\Model\User;

class CountsGateway extends Gateway
{

  /**
   * @var string
   */
  public static $api_url = "https://bapi.heia.kim/v1/counts";

  /**
   * @return object
   */
  public function get()
  {
    $options = [
      'http' => [
        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
      ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents(self::$api_url, false, $context);

    /**
     * Response failed?
     */
    if (!$response)
      return $this->error("API is not accessible at the moment");

    /**
     * @var object
     */
    $return_data = (object) [];

    /**
     * Processes basic data from bancho api.
     */
    $return_data = json_decode($response)?->data;

    /**
     * ? Users
     */
    if (!empty($return_data->users)) {
      $return_data->users->total = User::count();
      $return_data->users->restricted = User::where("priv", 2)->count();
    }

    /**
     * ? Squads
     */
    $return_data->squads = (object) [
      "total" => Squad::count(),
      "public" => Squad::where("joinable", Squad::$joinable_map["public"])->count(),
      "requestable" => Squad::where("joinable", Squad::$joinable_map["request"])->count(),
      "private" => Squad::where("joinable", Squad::$joinable_map["private"])->count(),
    ];

    /**
     * ? Beatmaps
     */
    $return_data->beatmaps = (object) [
      "total" => Beatmap::count(),
    ];

    /**
     * ? Scores
     */
    $return_data->scores = (object) [
      "total" => Score::count(),
    ];

    return $this->success(data: $return_data);
  }
}
