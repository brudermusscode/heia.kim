<?php

namespace Bruder\Heiakim\Enum;

enum Mod: int
{
  case NOMOD = 0;
  case NOFAIL = 1 << 0;
  case EASY = 1 << 1;
  case TOUCHSCREEN = 1 << 2;
  case HIDDEN = 1 << 3;
  case HARDROCK = 1 << 4;
  case SUDDENDEATH = 1 << 5;
  case DOUBLETIME = 1 << 6;
  case RELAX = 1 << 7;
  case HALFTIME = 1 << 8;
  case NIGHTCORE = 1 << 9;
  case FLASHLIGHT = 1 << 10;
  case AUTOPLAY = 1 << 11;
  case SPUNOUT = 1 << 12;
  case AUTOPILOT = 1 << 13;
  case PERFECT = 1 << 14;
  case KEY4 = 1 << 15;
  case KEY5 = 1 << 16;
  case KEY6 = 1 << 17;
  case KEY7 = 1 << 18;
  case KEY8 = 1 << 19;
  case FADEIN = 1 << 20;
  case RANDOM = 1 << 21;
  case CINEMA = 1 << 22;
  case TARGET = 1 << 23;
  case KEY9 = 1 << 24;
  case KEYCOOP = 1 << 25;
  case KEY1 = 1 << 26;
  case KEY3 = 1 << 27;
  case KEY2 = 1 << 28;
  case SCOREV2 = 1 << 29;
  case MIRROR = 1 << 30;

  /**
   * Get the mod as a string in short and long form.
   *
   * @return array The mod as a string in short and long form.
   */
  public function get_display()
  {
    return match ($this) {
      self::NOMOD => [
        "full" => "No Mod",
        "short" => "NM",
      ],
      self::NOFAIL => [
        "full" => "No Fail",
        "short" => "NF",
      ],
      self::EASY => [
        "full" => "Easy",
        "short" => "EZ",
      ],
      self::TOUCHSCREEN => [
        "full" => "Touchscreen",
        "short" => "TS",
      ],
      self::HIDDEN => [
        "full" => "Hidden",
        "short" => "HD",
      ],
      self::HARDROCK => [
        "full" => "HardRock",
        "short" => "HR",
      ],
      self::SUDDENDEATH => [
        "full" => "SuddenDeath",
        "short" => "SD",
      ],
      self::DOUBLETIME => [
        "full" => "DoubleTime",
        "short" => "DT",
      ],
      self::RELAX => [
        "full" => "Relax",
        "short" => "RX",
      ],
      self::HALFTIME => [
        "full" => "HalfTime",
        "short" => "HT",
      ],
      self::NIGHTCORE => [
        "full" => "Nightcore",
        "short" => "NC",
      ],
      self::FLASHLIGHT => [
        "full" => "Flashlight",
        "short" => "FL",
      ],
      self::AUTOPLAY => [
        "full" => "AutoPlay",
        "short" => "AU",
      ],
      self::SPUNOUT => [
        "full" => "SpunOut",
        "short" => "SO",
      ],
      self::AUTOPILOT => [
        "full" => "Autopilot",
        "short" => "AP",
      ],
      self::PERFECT => [
        "full" => "Perfect",
        "short" => "PF",
      ],
      self::KEY4 => [
        "full" => "Key4",
        "short" => "K4",
      ],
      self::KEY5 => [
        "full" => "Key5",
        "short" => "K5",
      ],
      self::KEY6 => [
        "full" => "Key6",
        "short" => "K6",
      ],
      self::KEY7 => [
        "full" => "Key7",
        "short" => "K7",
      ],
      self::KEY8 => [
        "full" => "Key8",
        "short" => "K8",
      ],
      self::FADEIN => [
        "full" => "FadeIn",
        "short" => "FI",
      ],
      self::RANDOM => [
        "full" => "Random",
        "short" => "RN",
      ],
      self::CINEMA => [
        "full" => "Cinema",
        "short" => "CN",
      ],
      self::TARGET => [
        "full" => "Target",
        "short" => "TP",
      ],
      self::KEY9 => [
        "full" => "Key9",
        "short" => "K9",
      ],
      self::KEYCOOP => [
        "full" => "KeyCoop",
        "short" => "CO",
      ],
      self::KEY1 => [
        "full" => "Key1",
        "short" => "K1",
      ],
      self::KEY3 => [
        "full" => "Key3",
        "short" => "K3",
      ],
      self::KEY2 => [
        "full" => "Key2",
        "short" => "K2",
      ],
      self::SCOREV2 => [
        "full" => "ScoreV2",
        "short" => "V2",
      ],
      self::MIRROR => [
        "full" => "Mirror",
        "short" => "MR",
      ],
    };
  }
}
