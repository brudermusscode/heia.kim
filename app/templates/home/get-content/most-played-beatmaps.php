<?php

use Heiakim\Model\Beatmap;
use Illuminate\Support\Collection;

$limit = 4;

/**
 * @var Collection<Beatmap>
 */
$MostPlayedBeatmaps = Beatmap::orderBy("plays", "DESC")
  ->groupBy("set_id")
  ->limit($limit)
  ->get();

foreach ($MostPlayedBeatmaps as $Beatmap)
  include TEMPLATE . "/beatmap/_beatmap-column.php";
