<?php

use Heiakim\Model\User;

if (CURRENT_PAGE === "u") {
  /**
   * @var int
   */
  $id   = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
  $mode = filter_input(INPUT_GET, "mode", FILTER_VALIDATE_INT) ?? "osu";

  /**
   * @var bool
   */
  $is_my_profile = $id === $CurrentUser->id;

  /**
   * @var User
   */
  $User = $is_my_profile
    ? $CurrentUser
    : User::find($id);

  if ($User) {
    $og->title = $User->name;
    $og->desc = "Join " . APP_NAME . " like $og->title and click circles to the rhythm of your favorite beatmaps";

    switch ($mode) {
      case 'taiko':
        $og->title .= ' × 🥁 ' . ucfirst($mode);
        break;

      case 'ctb':
        $og->title .= ' × 🍎 ' . "Catch the Beat";
        break;

      case 'mania':
        $og->title .= ' × 🎹 ' . ucfirst($mode);
        break;

      default:
      case 'osu':
        $og->title .= ' × 🔘 ' . "osu! Vanilla";
        break;
    }

    /**
     * The title.
     */
    $title = $og->title . ' on ' . APP_NAME;
  }
}
