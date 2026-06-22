<?php

namespace Heiakim\Model\User;

use Heiakim\Http\Request;
use Heiakim\Justin;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\User;

class UserSettingsPremium extends Justin
{
  /**
   * @var string
   */
  protected $table = "user_settings_premium";

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",
    "headline",
    "premium_name_style",
    "updated_at",
  ];

  /**
   * @var array
   */
  public static $premium_name_styles = [
    "none",
    "peach",
    "fire",
    "kassadin",
    "smaragd",
    "popel",
    "glitching",
    "valentine",
    "pinkus",
    "pastel",
    "incandescent",
    "try-my-luck",
    "dagoberts-style",
    "so-colorful",
    "vomit",
    "skin",
    "first-my-coffee",
    "durstloescher",
    "even-more-colorful",
    "the-winds",
    "looking-sick",
    "color-chunks",
    "OMG-MORE-COLORS",
    "oranges",
    "i-want-colorful-drinks",
    "kek",
    "pleasant-glow",
  ];

  /**
   * UPDATE
   *
   * @param object $params
   * @return object
   */
  public function edit(object $params)
  {
    /**
     * ? Name style
     */
    if (isset($params->premium_name_style) && !in_array($params->premium_name_style, self::$premium_name_styles, true))
      return $this->error();

    /**
     * ? HEADLINE
     */
    if (isset($params->headline)) {
      if (!filter_var($params->headline, FILTER_VALIDATE_INT))
        return $this->error();

      /**
       * Is already the headline?
       */
      if ($params->headline == $this->headline)
        return $this->error("<strong>This is your headline!</strong>");

      /**
       * @var ?Set
       */
      $Set = Set::find($params->headline);
      if (!$Set)
        return $this->error("<strong>This map doesn't exist.</strong>");
    }

    /**
     * Update it!
     */
    $this->update((array) $params);

    return $this->success("<strong>All set!</strong>");
  }

  /**
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
