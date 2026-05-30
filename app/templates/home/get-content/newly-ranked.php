<?php

use Bruder\Heiakim\Model\Beatmap;

/**
 * @var Beatmap
 */
$Beatmaps = Beatmap::where("status", 2)
  ->orderBy("last_update", "DESC")
  ->groupBy("set_id")
  ->limit(6)
  ->get();

/**
 * Card include appearances
 */
$include_all_diffs = false;
$include_time_ago = true;
$include_status = false;

foreach ($Beatmaps as $key => $Beatmap)
  include COMPONENT . "/beatmaps/_beatmap.php";

unset($Beatmaps, $include_status, $include_all_diffs, $include_time_ago);
