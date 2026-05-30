<?php

namespace Bruder\Heiakim\Trait;

trait Serialization
{
  /**
   * Keys that are valid for prequests even tho not explicitly
   * noted down in the controller
   *
   * @var array
   */
  protected $valid_passthrough_keys = [
    "habibi",
    "csrf_token",
  ];

  /**
   * Checks for given array keys being present in another array
   * and for array keys that are not allowed to be passed.
   *
   * @param array $necessary The keys needing to be present
   * @param array $post_params The array to check against
   * @param array $optional Let keys pass that are there but not filled
   * @return object|false
   */
  protected function serialize_request_params(array $necessary, array $post_params, array $optional = [])
  {
    /**
     * @var array
     */
    $always_pass = [
      "csrf_token",
      "habibi",
    ];

    // Check if all required parameters are set in the post request
    foreach ($necessary as $param)
      if (!isset($post_params[$param]))
        return false;

    // Check if any parameter in the post request is not in the required or optional arrays
    foreach ($post_params as $key => $value)
      if (!in_array($key, $necessary) && !in_array($key, $optional) && !in_array($key, $always_pass))
        return false;

    // Serialize parameters and respect arrays with strings
    $serializedParams = [];
    foreach ($post_params as $key => $value)
      if (is_array($value)) {
        $pre_serialized = htmlspecialchars(implode('<<<|/|>>>', $value));
        $serializedParams[$key] = explode("<<<|/|>>>", $pre_serialized);
      } else
        $serializedParams[$key] = htmlspecialchars($value);

    return (object) $serializedParams;
  }
}