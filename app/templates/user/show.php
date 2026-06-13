<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\Profile;
use Heiakim\Model\Stat;
use Heiakim\Model\User;
use Illuminate\Support\Collection;

/**
 * @var int
 */
$id   = filter_var($GLOBALS["route_param_id"] ?? 0, FILTER_VALIDATE_INT);

/**
 * @var string
 */
$mode = $GLOBALS["route_param_mode"] ?? null;

/**
 * @var string
 */
$mod = $GLOBALS["route_param_mod"] ?? null;

/**
 * @var string
 */
$more = $GLOBALS["route_param_more"] ?? null;

/**
 * @var bool
 */
$is_my_profile = $id === CurrentUser->id;

/**
 * @var User
 */
$User = $is_my_profile ? CurrentUser : User::find($id);

# Admins and elevated people can access any profile without restriction.
$bypass_restricted_screen = $User && (
  $is_my_profile || CurrentUser->priv > 4
);

# User doesn't exist?
if (!$User) :
  include UNAVAILABLE;

# User is restricted?
elseif ($User->is_restricted() && !$bypass_restricted_screen) :
  include __DIR__ . "/_restricted.php";

# Profile is not public?
elseif (!$User->privacy->is_public && !$bypass_restricted_screen) :
  include __DIR__ . "/_hidden.php";
else :

  $available = Gamemode::get_gamemode_possibilities();

  /**
   * Preferred gumode (mode + mod) as text strings.
   * @var object
   */
  $gumode_text = Gamemode::get_gumode_as_text($User->preferred_mode);

  # Use the mode set in GET or fallback to the Users preferred one.
  $mode = $mode ?? $gumode_text->mode;

  # Use the mod set in GET or fallback to the Users preferred one.
  $mod = $current_mod = $mod_global = $mod ?? $gumode_text->mod;

  /**
   * From mode and mod above, get the gumode as an int.
   * @var int
   */
  $gumode = Gamemode::get_gumode_as_int($mode, $mod);

  # No gumode could be determined from mode and mod?
  if ($gumode === null) :
    include UNAVAILABLE;
  else :

    # The :mode dynamic param is indeed dynamic here, as we determine in the follow-
    # ing, if the CurrentUser is viewing a sub page like photos or statistics or a
    # page of more scores/beatmaps like top scores, first scores and recent scores.
    # In the end, we keep only the $sub_page variable to include a corresponding tem-
    # plate.

    $sub_pages = [
      "overview",
      "photos",
      "statistics",
    ];

    # Determine, if a sub page is in view.
    $is_sub_page = in_array($mode, $sub_pages);
    $sub_page = $is_sub_page ? $mode : null;

    $more_pages = [
      "beatmaps",
      "performances-top",
      "performances-first",
      "performances-recent",
    ];

    # Determine, if a page from more is in view and set it to the $sub_page variable.
    $sub_page ??= in_array($more, $more_pages) ? $more : "overview";

    /**
     * @var object
     */
    $rankings = $User->get_rankings($gumode);
    $rank_development = $rankings->development;
    $has_played = $rankings->global !== null;
    $is_champion = $rankings->global === 1;

    # Determines, if the CurrentUser and the viewed User are both socially available
    # to the rest of the community.
    $both_sides_can_interact_socially =
      !$User->is_socially_excluded() && !CurrentUser->is_socially_excluded();

    /**
     * @var Stat
     */
    $Stats = $User->stats()
      ->where("mode", $gumode)
      ->first();

    # If the current user is following this user, touch the relationship instance to
    # reset activity in the feed.
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

    # Editor mode is exactly the same structure than the User profile, but does not
    # include some of the partials.
    if (!IS_EDIT_MODE) {

      # From the actual game.
      // $bancho_status = $User->get_bancho_game_status();
      // $is_online = $bancho_status->player_status->online ?? false;

      # + Page navigator.
      include_once __DIR__ . "/_page-navigator.php";

      # + Mode menu to choose from.
      include_once __DIR__ . "/_mode-menu.php";
    }

    # For when it's in Editor Mode.
    else {

      $User = CurrentUser;
      $gumode = 0;
      $is_my_profile = true;
      $rankings = $User->get_rankings(0);
    }

    # Add scores for our bot Aida. Should just happen once or can be uncommented to
    # redo it.
    // include_once __DIR__ . "/Aida/_add_scores.php";

    # + User header.
    include_once __DIR__ . "/_header.php";

    # + Mobile menu on bottom.
    include_once __DIR__ . "/_mobile-menu.php";

    $file_path = __DIR__ . "/pages/_$sub_page.php";

    # + Sub page ($more).
    include file_exists($file_path) ? $file_path : __DIR__ . "/pages/_index.php";

    # + Page end with nice birdy.
    include TEMPLATE . "/global/_scroll_end_logo.php";
  endif;
endif;
