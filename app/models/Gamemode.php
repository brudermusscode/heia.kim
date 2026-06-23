<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Utils\Arr;

class Gamemode extends Justin
{

  public static array $modes = [
    0, // standard vanilla
    1, // taiko
    2, // catch the beat
    3, // mania
    4, // standard > relax
    5, // taiko > relax
    6, // catch the beat > relax
    8, // standard > autopilot
  ];

  public static array $basic_modes = [
    0, // standard vanilla
    1, // taiko
    2, // catch the beat
    3, // mania
  ];

  /**
   * String representation of rulesets.
   */
  public static array $modes_text = [
    "osu",
    "taiko",
    "ctb",
    "mania"
  ];

  /**
   * String represenation of modifications.
   */
  public static array $mods_text = [
    "vanilla",
    "relax",
    "autopilot",
  ];

  public static array $mods_int_per_mode = [
    "osu" => [0, 4, 8],
    "taiko" => [1, 5],
    "ctb" => [2, 6],
    "mania" => [3],
  ];

  /**
   * All rulesets & possible combinations to form a gumode.
   *
   * @return object
   */
  public static function modes_w_mods()
  {

    $return = (object) [];
    $return->modes = [
      0 => "osu",
      1 => "taiko",
      2 => "ctb",
      3 => "mania",
    ];
    $return->mods = [
      "vanilla",
      "relax",
      "autopilot",
    ];
    $return->gumodes = [
      0 => ["mode" => "osu", "mod" => "vanilla"],
      4 => ["mode" => "osu", "mod" => "relax"],
      8 => ["mode" => "osu", "mod" => "autopilot"],
      1 => ["mode" => "taiko", "mod" => "vanilla"],
      5 => ["mode" => "taiko", "mod" => "relax"],
      2 => ["mode" => "ctb", "mod" => "vanilla"],
      6 => ["mode" => "ctb", "mod" => "relax"],
      3 => ["mode" => "mania", "mod" => "vanilla"]
    ];

    return $return;
  }

  /**
   * Some modes can only rank with less or more mods than others, which this function
   * will validate by returning either the mod if valid or a base mod »vanilla«.
   *
   * @param string $mod
   * @param string $mode
   * @return string
   */
  public static function validate_mod(string $mod, string $mode)
  {
    $base_mod = "vanilla";

    return match ($mode) {
      "standard",
      "standart",
      "osu" => in_array($mod, ["vanilla", "relax", "autopilot"]) ? $mod : $base_mod,
      "taiko",
      "ctb" => in_array($mod, ["vanilla", "relax"]) ? $mod : $base_mod,
      "mania" => in_array($mod, ["vanilla"]) ? $mod : $base_mod,
      default => $base_mod
    };
  }


  /**
   * @param string $mode
   * @return string
   */
  public static function valid_mods(string $mode)
  {
    return match ($mode) {
      "standard",
      "standart",
      "osu" => ["vanilla", "relax", "autopilot"],
      "taiko",
      "ctb" => ["vanilla", "relax"],
      "mania" => ["vanilla"],
      default => ["vanilla", "relax", "autopilot"],
    };
  }

  /**
   * Find the gumode by given string representations of a mode and a mod. If no mod
   * is given, it will return it's result as an array or the first element of it.
   *
   * @param string $mode
   * @param ?string $mod
   * @param bool $array
   * @return int|array
   */
  public static function find_gumode(
    ?string $mode,
    ?string $mod = null,
    bool $array = false
  ) {

    $gumode = match ($mode) {
      "osu" => match ($mod) {
        "vanilla" => 0,
        "relax" => 4,
        "autopilot" => 8,
        default => [0, 4, 8],
      },
      "taiko" => match ($mod) {
        "vanilla" => 1,
        "relax" => 5,
        default => [1, 5],
      },
      "ctb" => match ($mod) {
        "vanilla" => 2,
        "relax" => 6,
        default => [2, 6],
      },
      "mania" => match ($mod) {
        default => 3,
      },
      default => [0, 1, 2, 3, 4, 5, 6, 8],
    };

    return !$array && is_array($gumode) ? $gumode[0] : $gumode;
  }

  /**
   * Get the gumode combination from a given number.
   *
   * @param int $gumode The game mode from 0-8 (excl. 7)
   * @return object
   */
  public static function gumode_text(?int $gumode)
  {

    $return = [];
    $return["mode"] = match ($gumode) {
      0, 4, 8 => "osu",
      1, 5 => "taiko",
      2, 6 => "ctb",
      3 => "mania",
      default => "osu"
    };
    $return["mod"] = match ($gumode) {
      0, 1, 2, 3 => "vanilla",
      4, 5, 6 => "relax",
      8 => "autopilot",
      default => "vanilla",
    };

    return Arr::objectify($return);
  }

  /**
   * Turn the gumode to it's ruleset string representation.
   *
   * @param int $gumode
   * @return ?string
   */
  public static function gumode_to_mode(int $gumode)
  {
    return match ($gumode) {
      0, 4, 8 => "osu",
      1, 5 => "taiko",
      2, 6 => "ctb",
      3 => "mania",
      default => "osu",
    };
  }

  /**
   * String representation to use to include an icon from a given gumode as int.
   *
   * @param int $gumode
   * @return ?string
   */
  public static function gumode_icon(int $gumode)
  {
    return match ($gumode) {
      0, 4, 8 => "vanilla",
      1, 5 => "taiko",
      2, 6 => "ctb",
      3 => "mania",
      default => "vanilla"
    };
  }

  /**
   * Gets the ruleset as text from a given int. If the int doesn't fit in with stan-
   * dard osu! rulesets, it will try to find the gumode combination and return an ob-
   * ject, if found.
   *
   * @param mixed $mode
   * @return string|object
   */
  public static function mode_text(mixed $mode)
  {

    if ($mode <= 3)
      return match ($mode) {
        0 => "osu",
        1 => "taiko",
        2 => "ctb",
        3 => "mania",
        default => "osu"
      };
    else
      return self::gumode_text($mode);
  }

  /**
   * Turn the string of a ruleset into their int representation. Defaults to 0.
   *
   * @param string $mode
   * @return int
   */
  public static function mode_int(string $mode)
  {
    return match ($mode) {
      "standard", "osu" => 0,
      "taiko" => 1,
      "ctb" => 2,
      "mania" => 3,
      default => 0,
    };
  }

  /**
   * String representation to use to include an icon from a given mode as text.
   *
   * @param string $mode
   * @return string
   */
  public static function mode_icon(string $mode)
  {
    return match ($mode) {
      "osu" => "vanilla",
      default => $mode,
    };
  }

  /**
   * Converts the abbreviation of a mod into their full name.
   *
   * @param string $mod The mod with usually two characters long.
   * @return string The full mod's name.
   */
  public static function mod_full(string $mod)
  {
    return Score::mod_text($mod);
  }

  /**
   * Converts the short form name of a mode into the full version.
   *
   * @param string $mode The mode in short.
   * @return string The mode in long.
   */
  public static function mode_full(string $mode)
  {
    return $mode == "ctb"
      ? "Catch the Beat"
      : ($mode == "osu"
        ? self::__("Standard")
        : ucfirst($mode));
  }
}
