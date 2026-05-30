<?php

namespace Bruder\Heiakim\Enum;

enum BeatmapStatus: int
{
  case RANKED = 2;
  case LOVED = 5;

  /**
   * @return string
   */
  public function get_display()
  {
    return match ($this) {
      self::RANKED => "Ranked",
      self::LOVED => "Loved",
    };
  }
}