<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Utils\Arr;

class Gamemode extends Justin
{

  /**
   * @var array
   */
  public static $modes = [
    0, // standard vanilla
    1, // taiko
    2, // catch the beat
    3, // mania
    4, // standard > relax
    5, // taiko > relax
    6, // catch the beat > relax
    8, // standard > autopilot
  ];

  /**
   * @var array
   */
  public static $basic_modes = [
    0, // standard vanilla
    1, // taiko
    2, // catch the beat
    3, // mania
  ];

  /**
   * @var array
   */
  public static $modes_text = [
    "osu",
    "taiko",
    "ctb",
    "mania"
  ];

  /**
   * @var array
   */
  public static $mods_text = [
    "vanilla",
    "relax",
    "autopilot",
  ];

  /**
   * All basic gamemodes by their full text name
   *
   * @var array
   */
  public static $basic_modes_text = [
    "osu",
    "taiko",
    "ctb",
    "mania"
  ];

  /**
   * @var array
   */
  public static $mods_int_per_mode = [
    "osu" => [0, 4, 8],
    "taiko" => [1, 5],
    "ctb" => [2, 6],
    "mania" => [3],
  ];

  protected $returner = [
    "mode" => 'osu',
    "mod" => 'vanilla',
  ];

  /**
   * Gets the gamemode from int as text
   *
   * @param mixed $mode The mode as int
   * @param bool $gumode Optional if mods included
   * @return mixed String for normal modes or object from gumode
   */
  public static function get_mode_as_text(mixed $mode, bool $gumode = false)
  {
    if (!$gumode)
      return match ($mode) {
        0 => "osu",
        1 => "taiko",
        2 => "ctb",
        3 => "mania",
        default => "osu"
      };
    else
      return self::get_gumode_as_text($mode);
  }

  /**
   * Get the mode and mod from a given number representing the gumode
   *
   * @param int $gumode The game mode from 0-8
   * @return object The mode and mod
   */
  public static function get_gumode_as_text(int $gumode)
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
   * @param string $mode
   * @return int
   */
  public static function get_mode_as_int(string $mode)
  {
    return match ($mode) {
      "osu" => 0,
      "taiko" => 1,
      "ctb" => 2,
      "mania" => 3,
      default => 0,
    };
  }

  /**
   * Return the integer representation of a gamemode by given mode
   * and optionally the mod.
   *
   * @param string $mode The mode
   * @param string $mod The mod (optional)
   * @return int The gamemode as integer
   */
  public static function get_gumode_as_int(string $mode, ?string $mod = null)
  {
    switch ($mode) {
      case 'osu':
        if (!$mod || $mod === 'vanilla') return 0;
        if ($mod === 'relax') return 4;
        if ($mod === 'autopilot') return 8;
        return 0;
        break;

      case 'taiko':
        if (!$mod || $mod === 'vanilla') return 1;
        if ($mod === 'relax') return 5;
        return 1;
        break;

      case 'ctb':
        if (!$mod || $mod === 'vanilla') return 2;
        if ($mod === 'relax') return 6;
        return 2;
        break;

      case 'mania':
        if (!$mod || $mod === 'vanilla') return 3;
        return 3;
        break;

      default:
        return 0;
    }
  }

  /**
   * Returns all possiuble gamemodes with gumodes, which include
   * mods like relax and autopilot.
   *
   * @return object Gamemodes.
   */
  public static function get_gamemode_possibilities()
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
      0 => [
        "mode" => "osu",
        "mod" => "vanilla"
      ],

      4 => [
        "mode" => "osu",
        "mod" => "relax",
      ],

      8 => [
        "mode" => "osu",
        "mod" => "autopilot",
      ],

      1 => [
        "mode" => "taiko",
        "mod" => "vanilla",
      ],

      5 => [
        "mode" => "taiko",
        "mod" => "relax",
      ],

      2 => [
        "mode" => "ctb",
        "mod" => "vanilla",
      ],

      6 => [
        "mode" => "ctb",
        "mod" => "relax",
      ],

      3 => [
        "mode" => "mania",
        "mod" => "vanilla",
      ]
    ];

    return $return;
  }

  /**
   * Converts the gumode given to the representative name of the
   * icon used on the frontend.
   *
   * @param string $gumode
   * @return string
   */
  public static function convert_gumode_to_icon(int $gumode)
  {
    return match ($gumode) {
      0, 4, 8 => "vanilla",
      1, 5 => "taiko",
      2, 6 => "ctb",
      3 => "mania",
      default => null
    };
  }

  /**
   * Converts the mode given to the representative name of the
   * icon used on the frontend.
   *
   * @param string $mode
   * @return string
   */
  public static function convert_mode_to_icon(string $mode)
  {
    return match ($mode) {
      "osu" => "vanilla",
      default => $mode
    };
  }

  /**
   * Converts the abbreviation of a mod into their real name.
   *
   * @param string $mod The mod with usually two characters long.
   * @return string The full mod's name.
   */
  public static function convert_mod_to_full_name(string $mod)
  {
    return Score::get_full_mod_name_from($mod);
  }

  /**
   * Converts the short form name of a mode into the full version.
   *
   * @param string $mode The mode in short.
   * @return string The mode in long.
   */
  public static function convert_mode_to_full_name(string $mode)
  {
    return $mode == "ctb"
      ? "Catch the Beat"
      : ($mode == "osu"
        ? self::__("Standard")
        : ucfirst($mode));
  }

  /**
   * Gets the mode text (e. g. osu, taiko, ctb, mania) from the
   * gumode passed.
   *
   * @param int $gumode
   * @return ?string
   */
  public static function convert_gumode_to_mode_text(int $gumode)
  {
    return match ($gumode) {
      0, 4, 8 => "osu",
      1, 5 => "taiko",
      2, 6 => "ctb",
      3 => "mania",
      default => null
    };
  }

  /**
   * Gets the mode text (e. g. osu, taiko, ctb, mania) from the
   * gumode passed.
   *
   * @param int $gumode
   * @return object
   */
  public static function convert_gumode_to_mode_mod(int $gumode)
  {
    return (object) match ($gumode) {
      0 => [
        "mode" => "osu",
        "mod" => "vanilla"
      ],
      4 => [
        "mode" => "osu",
        "mod" => "relax"
      ],
      8 => [
        "mode" => "osu",
        "mod" => "autopilot"
      ],
      1 => [
        "mode" => "taiko",
        "mod" => "vanilla"
      ],
      5 => [
        "mode" => "taiko",
        "mod" => "relax"
      ],
      2 => [
        "mode" => "ctb",
        "mod" => "vanilla"
      ],
      6 => [
        "mode" => "ctb",
        "mod" => "relax"
      ],
      3 => [
        "mode" => "mania",
        "mod" => "vanilla"
      ],
      default => [
        "mode" => "osu",
        "mod" => "vanilla"
      ],
    };
  }

  /**
   * Returns the respective gumode from a combination of basic
   * string mode and mod. Will fallback to the first gumode of
   * the mode if any invalid combination is given or to 0, which
   * is osu! vanilla.
   *
   * @param string $mode
   * @param ?string $mod
   * @return int
   */
  public static function convert_mode_mod_to_gumode(?string $mode, ?string $mod)
  {
    return match ($mode) {
      "osu" => match ($mod) {
        "vanilla" => 0,
        "relax" => 4,
        "autopilot" => 8,
        default => 0,
      },
      "taiko" => match ($mod) {
        "vanilla" => 1,
        "relax" => 5,
        default => 1,
      },
      "ctb" => match ($mod) {
        "vanilla" => 2,
        "relax" => 6,
        default => 2,
      },
      "mania" => match ($mod) {
        default => 3,
      },
      default => 0,
    };
  }

  /**
   * Returns the respective gumode from a combination of basic
   * string mode and mod. Will fallback to all gumodes as an array
   * in the string given mode if any invalid combination is given,
   * or all gumodes as an array.
   *
   * @param string $mode
   * @param ?string $mod
   * @return int|array
   */
  public static function convert_mode_mod_to_gumode_array(?string $mode, ?string $mod)
  {
    match ($mode) {
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
  }

  /**
   * Returns all the available mods that, when combined with the
   * mode act as a whole own gamemode.
   *
   * @param string $mode
   * @return ?array
   */
  public static function mods_int_per_mode(string $mode)
  {
    return match ($mode) {
      "osu" => [0, 4, 8],
      "taiko" => [1, 5],
      "ctb" => [2, 6],
      "mania" => [3],
      default => null
    };
  }
}
