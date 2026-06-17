<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Trait\HasDefaultUser;
use Heiakim\Utils\Arr;

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

    pdie($params);

    /**
     * @var User
     */
    $CurrentUser = $this->user;

    # ? Profile Picture.
    if (!empty($params->files["image"]["tmp_name"])) {
      $CurrentUser->settings->upload_profile_picture($params->files["image"]);
      $image_updated = true;
    }

    # ? Wrapper
    $pprofile = $params->profile;

    # Validate no wrapper is missing.
    foreach (self::$wrapper as $wrapper)
      if (empty($pprofile[$wrapper]))
        return error("Missing profile wrapper.");

    # Validate wrappers are all valid.
    foreach ($pprofile as $wrapper => $array)
      if (!in_array($wrapper, self::$wrapper))
        return error("Invalid wrapper found: <strong>$wrapper</strong>.");

    # ? Tabs
    $tabs = $pprofile["tabs_visibility"];

    # Validate tabs are all valid.
    if (count($tabs) !== count(self::$tabs_visibility))
      return error("Tabs: Length not passing: <strong>Found " . count($tabs) . ", expected " . count(self::$tabs_visibility) . "</strong>.");

    # Validate only numeric values in tab objects.
    if (!Arr::validate_numeric_values($tabs))
      return error("Tabs: Non numeric value found.");

    # Any invalid object contained?
    foreach ($tabs as $tab => $value)
      if (!array_key_exists($tab, self::$tabs_visibility))
        return error("Tabs: Invalid object found: <strong>$tab</strong>");

    # Set new config!
    $CurrentUser->profile->tabs_visibility = json_encode($tabs);

    # ? Sections
    $sections = $pprofile["sections_visibility"];

    # Check for all columns to be present.
    if (count($sections) !== count(self::$sections_visibility))
      return error("Invalid length of sections.");

    $sent_objects = [];

    # Create an array of all sent objects inside sections.
    foreach ($sections as $key => $section)
      foreach ($section as $object => $value)
        $sent_objects[] = $object;

    $valid_objects = array_merge(self::$small_columns, self::$large_columns);

    # Check sent objects against all merged valid objects.
    foreach ($sent_objects as $object)
      if (!in_array($object, $valid_objects))
        return error("Sections: Invalid object found: <strong>$object</strong>");

    $column_sizes = self::$columns;

    # Check, if any object inside the section is not of invalid size.
    foreach ($sections as $key => $column) {
      $current_column_size = $column_sizes[$key] ?? null;

      # Section exists/is valid?
      if (!$current_column_size)
        return $this->error("Sections: Invalid column id: <strong>$key</strong>");

      # Unset the key so that if one column id has been sent twice, it won't pass
      # anymore.
      unset($column_sizes[$key]);

      foreach ($column as $object => $value) {

        # Invalid small object?
        if ($current_column_size === "small" && !in_array($object, self::$small_columns))
          return error("Invalid object in »small« section: <strong>$object</strong>");

        # Invalid large object?
        if ($current_column_size === "large" && !in_array($object, self::$large_columns))
          return error("Invalid object in »large« section: <strong>$object</strong>");
      }
    }

    # Validate values of the array. They should all be numeric.
    if (!Arr::validate_numeric_values($sections))
      return $this->error("Sections: Non numeric value found.");

    # Set sections!
    $CurrentUser->profile->sections_visibility = Arr::to_json($sections);

    # Save it!
    $CurrentUser->profile->save();

    $image_updated_message = " Press &nbsp; <span tag text smol bold>CTRL + F5</span> &nbsp; to show your profile picture immediately. Otherwise it might take some time to show up.";

    return success("<strong>Freshly installed!</strong>" . (isset($image_updated) ? $image_updated_message : ""));
  }

  /**
   * @return object
   */
  public function decoded()
  {
    return $this->user->decoded_profile();
  }

  /**
   * @param string $wrapper
   * @param string $object
   * @param int $new_value
   * @return object
   */
  public function update_value_for(string $wrapper, string $object, int $new_value)
  {

    # Invalid wrapper?
    if (!in_array($wrapper, $this->wrapper))
      return error("<strong>Invalid wrapper.</strong>");

    # Invalid object inside wrapper?
    if (!array_key_exists($object, $this->$wrapper))
      return error("<strong>Invalid object.</strong>");

    /**
     * @var object
     */
    $DecodedProfileWrapper = $this->decoded()->$wrapper;

    # Update the value.
    $DecodedProfileWrapper[$object] = $new_value == 0 ? 0 : 1;
    $this->$wrapper = $DecodedProfileWrapper;

    # Save it!
    $this->save();

    return success();
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
      "beatmaps_played" => __("Most played beatmaps"),
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
   * @return void
   *
   * NOTE: Includes /app/templates/my/editor/placeholders/{file_name}.php
   */
  public function include_object_placeholder(string $object_name)
  {

    $sections = $this->sections_visibility;
    $object = Arr::find_key(
      $object_name,
      json_decode($this->sections_visibility, true)
    );
    $object_name = $object_name;
    $object_visibility = $object[$object_name];

    // TODO: Make it dynamic. By now it just uses the default sections wrapper.
    $wrapper = self::$wrapper[1];

    # Set variable $Profile to make it available in the included template.
    $Profile = $this;

    return include ROOT . "/app/templates/my/editor/placeholders/_" .
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
   * Returns, whether or not a bool value of an object inside a given column is true
   * or false.
   *
   * @param string $section
   * @param string $object
   * @return bool
   */
  public function bool_value(string $section, string $object)
  {
    $c = $this->decoded()->$section;

    return isset($c[$object]) && $c[$object] == 0 ? 0 : 1;
  }
}
