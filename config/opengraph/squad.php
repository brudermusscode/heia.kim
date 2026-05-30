<?php

use Bruder\Heiakim\Model\Squad;

if (CURRENT_PAGE === 'squad') {

  /**
   * @var int
   */
  $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

  /**
   * @var ?Squad
   */
  $Squad = Squad::find($id);

  if ($Squad) {
    $og->title = $Squad->name;
    $og->desc = $Squad->name . " is a squad on " . APP_NAME . ". Join or create one yourself and climb the leaderboard together with friends!";
    $og->image = CLAN_IMAGE_URL . "/headline-images/" . $Squad->image;

    $title = "🐉 " . $og->title . ' on ' . APP_NAME;
  }
}
