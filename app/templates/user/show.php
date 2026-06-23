<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\Profile;
use Heiakim\Model\Stat;
use Heiakim\Model\User;

$id = aglobal("id");
$sub = aglobal("sub");
$mode = aglobal("mode") ?? "osu";
$mod = aglobal("mod") ?? "vanilla";

# Declaring this variable so early will make profile loads for the CurrentUser faster,
# as the User doesn't have to be fetched.
$is_my_profile = $id === CurrentUser->id;

/**
 * @var User
 */
$User = $is_my_profile ? CurrentUser : User::with("profile")->find($id);

redirect_unauthorized($User);

# Admins and elevated people should be able to access any profile without restriction.
$bypass_restricted_screen = $User && (
  $is_my_profile || CurrentUser->priv > 4
);

if ($User->is_restricted() && !$bypass_restricted_screen) :
  include __DIR__ . "/_restricted.php";

elseif (!$User->privacy->is_public && !$bypass_restricted_screen) :
  include __DIR__ . "/_hidden.php";

else :

  /**
   * @var object
   */
  $gumode_text = Gamemode::gumode_text($User->preferred_mode);
  $mode ??= $gumode_text->mode;
  $mod ??= $gumode_text->mod;
  $mod = Gamemode::validate_mod($mod, $mode);
  $valid_mods = Gamemode::valid_mods($mode);
  $current_mod = $mod;
  $gumode = Gamemode::find_gumode($mode, $mod);

  if ($gumode === null) :
    include UNAVAILABLE;
  else :

    # The dynamic :sub param is indeed dynamic here, as we determine in the follow-
    # ing, if the CurrentUser is viewing a sub page like photos or statistics or a
    # page of more scores/beatmaps like top scores, first scores and recent scores.
    # In the end, we keep only the sub page variable to include a corresponding tem-
    # plate.

    $sub_pages = [
      "overview",
      "photos",
      "statistics",
      "beatmaps",
      "performances-top",
      "performances-first",
      "performances-recent",
    ];

    $sub = in_array($sub, $sub_pages) ? $sub : "overview";

    /**
     * @var object
     */
    $rankings = $User->get_rankings($gumode);

    $rank_development = $rankings->development;
    $has_played = $rankings->global !== null;
    $is_champion = $rankings->global === 1;
    $both_sides_can_interact_socially =
      !$User->is_socially_excluded() && !CurrentUser->is_socially_excluded();

    /**
     * @var Stat
     */
    $Stats = $User->stats()
      ->where("mode", $gumode)
      ->first();

    # If the current user is following this user, touch the relationship instance to
    # reset activity in the CurrentUser's home feed. Important to note is that a rela-
    # tion can be one sided. So when touching the realtionship the CurrentUser has
    # triggered, it will only be updated for the CurrentUser.
    if (!$is_my_profile && LOGGED) {
      if (CurrentUser->follows($User))
        CurrentUser->followings()
          ->where("user2", $User->id)
          ->first()
          ->pivot
          ->update([
            "updated_at" => CURRENT_TIMESTAMP,
          ]);
    }

    /**
     * @var Profile
     */
    $Profile = $User->profile;

    $base_url = "/u/$User->id";

    include __DIR__ . "/_page-navigator.php";

    $base_url .= "/$sub";

    # From the actual game.
    // $bancho_status = $User->get_bancho_game_status();
    // $is_online = $bancho_status->player_status->online ?? false;

    # + Mode menu should only be shown in sub pages that have scores to show.
    if (!in_array($sub, ["photos"]))
      include COMPONENT . "/_mode-menu.php";

    # Add scores for our bot Aida. Should just happen once or can be uncommented to
    # redo it.
    // include __DIR__ . "/Aida/_add_scores.php";
    include __DIR__ . "/_header.php";
    include __DIR__ . "/_mobile-menu.php";

    # TODO: Add relationship actions for mobile devices.

    # Include the sub page if it exists, or fallback to index.
    $file_path = __DIR__ . "/pages/_$sub.php";
    include file_exists($file_path) ? $file_path : __DIR__ . "/pages/_index.php";

    # + Page end with nice birdy.
    include TEMPLATE . "/global/_scroll_end_logo.php";
  endif;
endif;
