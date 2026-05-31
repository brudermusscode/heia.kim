<?php

namespace Heiakim\File;

use Heiakim\Utils\Arr;

class JSON
{
  /**
   * Convert a given array to a JSON string.

   * @param array $array The array to be converted to JSON format.
   * @return string The JSON representation of the input array.
   */
  public static function from_array(array $array)
  {
    return Arr::to_json($array);
  }

  /**
   * Read and parse the JSON configuration file.
   *
   * @param string $filePath the path to the JSON configuration file.
   * @return object the configuration array.
   */
  public static function read($filePath)
  {
    $json = file_get_contents($filePath);
    return (object) json_decode($json);
  }
}
