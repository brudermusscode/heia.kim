<?php

namespace Bruder\Heiakim\Model\Beatmap;

use Bruder\Justin;

class SetArtist extends Justin
{
  /**
   * @var string
   */
  protected $table = "mapset_artists";

  /**
   * @var array
   */
  protected $fillable = [
    "mapset_id",
    "artist_id",
    "updated_at",
  ];
}
