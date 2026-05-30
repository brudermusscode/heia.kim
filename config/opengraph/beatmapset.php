<?php

use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Beatmap\Set;

/**
 * Build OpenGraph elements for beatmap set pages.
 */

if (CURRENT_PAGE === 'beatmapset') {
  /**
   * Get params
   */
  $set_id = filter_input(INPUT_GET, "set_id", FILTER_VALIDATE_INT) ?? 0;
  $map_id = filter_input(INPUT_GET, "map_id", FILTER_VALIDATE_INT) ?? 0;
  $mode   = filter_input(INPUT_GET, "mode", FILTER_SANITIZE_SPECIAL_CHARS);
  $mod = $current_mod = filter_input(INPUT_GET, "mod", FILTER_SANITIZE_SPECIAL_CHARS);

  /**
   * @var Set
   */
  $Set = Set::with("beatmaps.scores.user")
    ->with("artists")
    ->find($set_id);

  /**
   * @var Beatmap
   */
  $Beatmap = $Set?->beatmaps
    ->where("id", $map_id)
    ->first();

  if ($Set && $Beatmap) {
    /**
     * @var string
     */
    $OpengraphArtist = $Beatmap->get_featured_artist_names()[0];

    $opengraph_beatmap_title = $Beatmap->title;
    $opengraph_beatmap_artist = htmlspecialchars($OpengraphArtist);
    $opengraph_beatmap_status = $Beatmap->turn_status_to_text();

    /**
     * OpenGraph object
     */
    $og->title = "🎵 $opengraph_beatmap_title - $opengraph_beatmap_artist";
    $og->desc  = "See scores being set on this beatmap!";
    $og->image = "https://assets.ppy.sh/beatmaps/$set_id/covers/cover@2x.jpg";

    switch ($opengraph_beatmap_status) {
      case "Ranked":
        $og->title .= " × 🏅 Ranked";
        break;

      case "Loved":
        $og->title .= " × ❤️ Loved";
        break;

      case "Approved":
        $og->title .= " × ✅ Approved";
        break;

      case "Qualified":
        $og->title .= " × 🔖 Qualified";
        break;

      case "Pending":
        $og->title .= " × ⌛️ Pending";
        break;

      default:
        break;
    }

    /**
     * The title
     */
    $title = $og->title . " on " . APP_NAME;
  }
}
