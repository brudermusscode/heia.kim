<?php

/**
 * Add up scores, maps and artists for our bot Aida so the profile
 * looks like one of a real player.
 */

use Bruder\Database\Manager as DBM;

$honeyworks_maps = (new DBM)->select(
  "SELECT * FROM maps WHERE artist LIKE '%honeyworks%'",
  [],
  true
);

foreach ($honeyworks_maps as $m) {

  $possible_score = 0;
  for ($i = 1; $i <= $m->max_combo; $i++)
    $possible_score += $i * 300;

  $map_md5 = $m->md5;
  $score = rand($possible_score / 2, $possible_score);
  $pp = rand(300, 699);

  $min = 98;
  $max = 100;
  $acc = $min + mt_rand() / mt_getrandmax() * ($max - $min);

  $max_combo = $m->max_combo;

  /**
   * mods
   */
  $all_mods = [0, 88, 72, 200, 216,];
  $mods = $all_mods[array_rand($all_mods)];

  /**
   * Just allow 18 % of diff
   */
  $percent = (int) number_format((1.18 * $m->max_combo) - $m->max_combo, 0);

  $ndiff = rand(0, $percent);
  $n300 = $m->max_combo - $ndiff;
  $n100 = $ndiff;
  $n50 = 0;
  $nmiss = 0;
  $ngeki = 0;
  $nkatu = 0;

  /**
   * grade
   */
  $all_grades = ["A", "S", "SH", "XH"];
  $grade = $all_grades[array_rand($all_grades)];

  /**
   * When hidden mod is enabled, it's gonna be SH grade
   */
  if (in_array($mods, [200, 216, 72, 88]))
    $grade = "SH";

  /**
   * Full combo with all 300
   * Perfect
   */
  if ($n300 == $m->max_combo)
    $grade = "XH";

  /**
   * perfect score
   */
  if ($grade == "XH") {
    $acc = 100.00;
    $max_combo = $m->max_combo;
  }

  $status = 2;

  /**
   * mode
   */
  if (in_array($mods, [0, 72, 88]))
    $mode = 0;

  if (in_array($mods, [200, 216]))
    $mode = 4;

  /**
   * play_time
   * Random between 1 year ago and today
   */
  $startDate = strtotime('-1 year');
  $endDate = time();
  $randomTimestamp = mt_rand($startDate, $endDate);
  $play_time = date('Y.m.d H:i:s', $randomTimestamp);

  $time_elapsed = rand(9072, 118293);

  $client_flags = 0;
  $user_id = 1;

  /**
   * It's perfect
   */
  if ($max_combo == $m->max_combo) {
    $perfect = 1;
  }

  $online_checksum = "91d6a9178ebc553a521f2d7f29663c50";

  $stmt = (new DBM)->insert(
    "INSERT INTO scores
      (
        map_md5,
        score,
        pp,
        acc,
        max_combo,
        mods,
        n300,
        n100,
        n50,
        nmiss,
        ngeki,
        nkatu,
        grade,
        status,
        mode,
        play_time,
        time_elapsed,
        client_flags,
        userid,
        perfect,
        online_checksum
      )
      VALUES
      (
        '$map_md5',
        '$score',
        '$pp',
        '$acc',
        '$max_combo',
        '$mods',
        '$n300',
        '$n100',
        '$n50',
        '$nmiss',
        '$ngeki',
        '$nkatu',
        '$grade',
        '$status',
        '$mode',
        '$play_time',
        '$time_elapsed',
        '$client_flags',
        '1',
        '$perfect',
        '$online_checksum'
      )"
  );
}
