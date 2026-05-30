<?php

use Bruder\Heiakim\Model\Artist;

/**
 * Build OpenGraph elements for artist pages.
 */

if (CURRENT_PAGE === "artist") {
  /**
   * Get params
   */
  $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

  /**
   * @var int
   */
  $fetch_count = 40;

  /**
   * @var ?Artist
   */
  $Artist = Artist::with(["beatmapsets" => function ($q) use ($fetch_count) {
    $q->limit($fetch_count);
  }])
    ->with("feedback")
    ->find($id);

  if ($Artist) {
    $og->title = htmlspecialchars_decode($Artist->name);
    $og->desc = "All the beatmaps from this artist at one place";

    /**
     * The title.
     */
    $title = "🎤 $og->title on " . APP_NAME;
  }
}