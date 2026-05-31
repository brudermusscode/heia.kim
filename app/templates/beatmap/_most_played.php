<?php

use Heiakim\Model\Beatmap;

/**
 * @var int
 */
$limit ??= 6;

/**
 * @var Beatmap
 */
$MostPlayedBeatmaps = Beatmap::orderBy("plays", "DESC")
  ->groupBy("set_id")
  ->limit($limit)
  ->get();

foreach ($MostPlayedBeatmaps as $Beatmap)
  include TEMPLATE . "/components/beatmaps/_beatmap.php";
