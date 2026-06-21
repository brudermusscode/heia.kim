<?php

use Heiakim\Model\Beatmap;
use Illuminate\Support\Collection;

$limit = 4;

/**
 * @var Collection<Beatmap>
 */
$Beatmaps = Beatmap::where("status", 5)
  ->orderBy("last_update", "DESC")
  ->groupBy("set_id")
  ->limit($limit)
  ->get();

foreach ($Beatmaps as $key => $Beatmap)
  include TEMPLATE . "/beatmap/_beatmap-column.php";
