<?php

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Model\Beatmap;
use Heiakim\Model\User;
use DateTime;

class Feed extends Justin
{
  /**
   * @var User
   */
  protected $CurrentUser;

  public function __construct(User $CurrentUser)
  {
    $this->CurrentUser = $CurrentUser;
  }

  /**
   * @param array $mode
   * @param array $status
   * @param string $order
   * @param string $sort
   * @param int $limit
   * @return Beatmap
   */
  public function beatmaps(
    array $mode = [0, 1, 2, 3],
    array $status = [-1, 0, 1, 2, 3, 4, 5],
    string $order = "last_update",
    string $sort = "DESC",
    int $limit = 60
  ) {
    return Beatmap::with("set")
      ->whereIn("mode", $mode)
      ->whereIn("status", $status)
      ->groupBy("set_id")
      ->orderBy($order, $sort)
      ->limit($limit)
      ->get();
  }

  /**
   * Fetches beatmaps based on scores that have been set inside
   * the timespan of the current day from start to the end.
   *
   * @param int $limit
   * @return ?Beatmap
   */
  public function most_played_today(int $limit = 60)
  {
    $today = new DateTime('today');
    $startDate = $today->setTime(0, 0, 0);
    $endDate = (clone $today)->setTime(23, 59, 59);

    return Beatmap::withCount(['scores' => function ($query) use ($startDate, $endDate) {
      $query->whereBetween('play_time', [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')]);
    }])
      ->having('scores_count', '>', 0)
      ->orderBy('scores_count', "DESC")
      ->limit($limit)
      ->get();
  }
}
