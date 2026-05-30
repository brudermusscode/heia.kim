<?php

// TODO: Custom -> Headline fade optional.
// TODO: Custom -> Move profile picture and name anywhere.

namespace Bruder\Heiakim\Model;

use Bruder\Justin;
use Bruder\Http\Request;
use Bruder\Heiakim\Trait\HasDefaultUser;
use Bruder\Utils\Arr;

class Profile extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "tabs_visibility",
    "sections_visibility",
    "updated_at",
  ];

  /**
   * Current page structure (this could change).
   *
   * |--- small column
   * |   |--- object-1
   * |   |--- ...
   * |
   * |--- large column
   * |   |--- object-1
   * |   |--- ...
   * |
   * |--- small column
   * |   |--- object-1
   * |   |--- ...
   */

  /**
   * @var array
   */
  public static $wrapper = [
    "tabs_visibility",
    "sections_visibility",
  ];

  /**
   * @var array
   */
  public static $tabs_visibility = [
    "premium" => 1,
    "statistics" => 1,
    "photos" => 1,
  ];

  /**
   * @var array
   */
  public static $sections_visibility = [
    0 => [
      "squad" => 1,
      "followers" => 1,
      "followings" => 1,
    ],
    1 => [
      "graph" => 1,
      "scores_pinned" => 1,
      "scores_top" => 1,
      "scores_first" => 1,
      "scores_recent" => 1,
      "beatmaps_played" => 1,
    ],
    2 => [
      "statistics" => 1,
      "artists_played" => 1,
    ],
  ];

  /**
   * @var array
   */
  public static $columns = [
    "0" => "small",
    "1" => "large",
    "2" => "small",
  ];

  /**
   * @var array
   */
  protected static $large_columns = [
    "graph",
    "scores_pinned",
    "scores_top",
    "scores_first",
    "scores_recent",
    "beatmaps_played",
  ];

  /**
   * @var array
   */
  protected static $small_columns = [
    "squad",
    "followers",
    "followings",
    "artists_played",
    "statistics",
  ];

  /**
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * Validate the image. If one is set, the name field will be
     * filled. Otherwise it's probably empty so we can skip it.
     */
    if (isset($params->files) && is_array($params->files) && $params->files["name"]) {

      /**
       * Upload the new image.
       */
      $CurrentUser->settings
        ->upload_profile_picture($params);

      /**
       * @var bool
       */
      $image_updated = true;
    }

    /**
     * @var array[]
     */
    $pprofile = $params->profile;

    /**
     * Check for any invalid section which respresent groups of
     * elements of the same kind.
     */
    foreach ($pprofile as $wrapper => $array)
      if (!in_array($wrapper, self::$wrapper))
        return $this->error("Invalid wrapper found: <strong>$wrapper</strong>.");

    /**
     * ? Tabs Visibility
     *
     * @var array
     */
    $tabs = $pprofile["tabs_visibility"];

    if (!isset($tabs) || count($tabs) !== count(self::$tabs_visibility))
      return $this->error("Tabs: Length not passing: <strong>Found " . count($tabs) . ", expected " . count(self::$tabs_visibility) . "</strong>.");

    /**
     * Check for only numeric values.
     */
    if (!Arr::validate_numeric_values($tabs))
      return $this->error("Tabs: Non numeric value found.");

    /**
     * Any invalid object in here?
     */
    foreach ($tabs as $tab => $value)
      if (!array_key_exists($tab, self::$tabs_visibility))
        return $this->error("Tabs: Invalid object found: <strong>$tab</strong>");

    /**
     * Premium tab disabled & user is permitted to?
     */
    if (!$tabs["premium"] && !$CurrentUser->is_premium())
      return $this->error("<strong>You have disabled tabs, that only Premium+ members can.</strong>");

    /**
     * Set the new json string config.
     */
    $CurrentUser->profile->tabs_visibility = Arr::to_json($tabs);

    /**
     * ? Sections Visibility
     *
     * @var array
     */
    $columns = $pprofile["sections_visibility"];

    /**
     * Check for all sections to be present.
     */
    if (!isset($columns) || count($columns) !== count(self::$sections_visibility))
      return $this->error("Sections: Length not passing: <strong>Found " . count($columns) . ", expected " . count(self::$sections_visibility) . "</strong>.");

    /**
     * @var array
     */
    $sent_objects = [];

    foreach ($columns as $key => $section)
      foreach ($section as $object => $value)
        $sent_objects[] = $object;

    /**
     * @var array
     */
    $valid_objects = array_merge(self::$small_columns, self::$large_columns);

    /**
     * Check sent objects against all merged valid objects. If any is there,
     * that is not valid, return an error.
     */
    foreach ($sent_objects as $object)
      if (!in_array($object, $valid_objects))
        return $this->error("Sections: Invalid object found: <strong>$object</strong>");

    /**
     * @var ?string
     */
    $column_sizes = self::$columns;

    /**
     * Check, if any object inside the section is not of permitted
     * size (large/small).
     */
    foreach ($columns as $key => $column) {
      $current_column_size = $column_sizes[$key] ?? null;

      /**
       * Section exists/is valid?
       */
      if (!$current_column_size)
        return $this->error("Sections: Invalid column id: <strong>$key</strong>");

      /**
       * Unset the key so that if one column id has been sent
       * twice, it won't pass anymore.
       */
      unset($column_sizes[$key]);

      /**
       * Go through all objects in this section.
       */
      foreach ($column as $object => $value) {
        /**
         * If it's a small section, is it in a valid column?
         */
        if ($current_column_size === "small" && !in_array($object, self::$small_columns))
          return $this->error("Sections: Object in invalid column: <strong>$object</strong>");

        /**
         * If it's a large section, is it in a valid column?
         */
        if ($current_column_size === "large" && !in_array($object, self::$large_columns))
          return $this->error("Sections: Object in invalid column: <strong>$object</strong>");
      }
    }

    /**
     * Validate values of the array. They should all be numeric.
     */
    if (!Arr::validate_numeric_values($columns))
      return $this->error("Sections: Non numeric value found.");

    /**
     * Set the new json string config.
     */
    $CurrentUser->profile->sections_visibility = Arr::to_json($columns);

    /**
     * Update it!
     */
    $CurrentUser->profile->save();

    /**
     * @var string
     */
    $image_updated_message = " It can take some time for your new profile picture to show up. Pressing &nbsp; <span tag text smol bold>CTRL + F5</span> &nbsp; refreshes the cache of your browser. This way your image will be updated immediately.";

    return $this->success("<strong>Freshly installed!</strong>" . (isset($image_updated) ? $image_updated_message : ""));
  }

  /**
   * @return object
   */
  public function decoded()
  {
    return $this->user
      ->decoded_profile();
  }

  /**
   * @param string $wrapper
   * @param string $object
   * @param int $value
   * @return object
   */
  public function update_value_for(string $wrapper, string $object, int $new_value)
  {
    /**
     * Wrapper exists?
     */
    if (!in_array($wrapper, $this->wrapper))
      return $this->error("<strong>Invalid wrapper.</strong>");

    /**
     * Object exists inside the wrapper?
     */
    if (!array_key_exists($object, $this->$wrapper))
      return $this->error("<strong>Invalid object.</strong>");

    /**
     * @var object
     */
    $DecodedProfileWrapper = $this->decoded()->$wrapper;

    /**
     * Update the value.
     */
    $DecodedProfileWrapper[$object] = $new_value == 0 ? 0 : 1;
    $this->$wrapper = $DecodedProfileWrapper;

    /**
     * Save it to the database.
     */
    $this->save();

    return Request::modoru($this->return);
  }

  /**
   * @param string $object_name
   * @return string
   */
  public function get_object_title(string $object_name)
  {
    return match ($object_name) {
      "graph" => __("Ranking Graph"),
      "scores_pinned" => __("Pinned"),
      "scores_top" => __("Highest performances"),
      "scores_first" => __("First places"),
      "scores_recent" => __("Recent"),
      "beatmaps_played" => __("Beatmaps, you play alot"),
      "squad" => "Squad",
      "followers" => __("Followers"),
      "followings" => __("Following"),
      "artists_played" => __("Artists"),
      "statistics" => __("Statistics"),
      default => "",
    };
  }


  /**
   * @param string $object_name
   * @return include /app/templates/my/editor/placeholders/{file_name}.php
   */
  public function include_object_placeholder(string $object_name)
  {
    /**
     * @var ?array
     */
    $object = Arr::find_key($object_name, json_decode($this->sections_visibility, true));
    $object_name = $object_name;
    $object_visibility = $object[$object_name];

    /**
     * @var self
     */
    $Profile = $this;

    /**
     * @var string
     */
    // TODO: Make it dynamic. By now it just uses the default sections wrapper.
    $wrapper = self::$wrapper[1];

    return include _root() . "/app/templates/my/editor/placeholders/_" .
      match ($object_name) {
        "graph" => "graph",
        "scores_pinned",
        "scores_top",
        "scores_first",
        "scores_recent" => "scores",
        "beatmaps_played" => "beatmaps",
        "squad" => "squad",
        "followers",
        "followings" => "users",
        "artists_played" => "artists",
        "statistics" => "statistics",
        default => "default",
      }
      . ".php";
  }

  /**
   * Returns, whether or not a bool value of an object inside a
   * given column is true or false.
   *
   * @param string $section
   * @param string $object
   * @return bool
   */
  public function bool_value(string $column, string $object)
  {
    $c = $this->decoded()
      ->$column;

    return isset($c[$object]) && $c[$object] == 0 ? 0 : 1;
  }
}
