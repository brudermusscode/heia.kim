<?php

use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Profile;
use Bruder\Heiakim\Model\User;

/**
 * @var User $CurrentUser
 */

/**
 * @var int
 */
$id   = filter_var(GET->id ?? 0, FILTER_VALIDATE_INT);
$mode = filter_var(GET->mode ?? "osu", FILTER_VALIDATE_INT);

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

/**
 * Whether or not the viewing user can bypass the restricted
 * screen and see the profile of the restricted user.
 */
$bypass_restricted_screen = $User && ($User->id === $CurrentUser->id || $CurrentUser->priv > 4);

/**
 * User doesn't exist?
 */
if (!$User)
  include UNAVAILABLE;

/**
 * User is restricted?
 */
else if ($User->is_restricted() && !$bypass_restricted_screen)
  include __DIR__ . "/_restricted.php";

/**
 * Profile is not public?
 */
else if (!$User->privacy->is_public && !$bypass_restricted_screen)
  include __DIR__ . "/_hidden.php";
else {

  $available = Gamemode::get_gamemode_possibilities();

  /**
   * Preferred gamemode
   *
   * @var object
   */
  $gumode_text = Gamemode::get_gumode_as_text($User->preferred_mode);

  /**
   * Gamemode and gumode
   */
  $mode = filter_var(GET->mode ?? $gumode_text->mode, FILTER_SANITIZE_SPECIAL_CHARS);
  $mod =
    $current_mod =
    $mod_global = filter_var(GET->mod ?? $gumode_text->mod, FILTER_SANITIZE_SPECIAL_CHARS);
  $gumode = Gamemode::get_gumode_as_int($mode, $mod);

  if ($gumode === null)
    include UNAVAILABLE;
  else {

    /**
     * Whether or not the current page is one of the new sub pages
     * like photos.
     *
     * @var bool
     */
    $is_sub_page = false;

    /**
     * @var ?string
     */
    $sub_page = null;

    /**
     * @var array
     */
    $sub_pages = [
      "photos",
    ];

    /**
     * Determine, if a sub page is in view.
     */
    if (!in_array($mode, Gamemode::$modes_text) && in_array($mode, $sub_pages)) {
      $sub_page = $mode;
      $is_sub_page = true;
    }

    /**
     * @var array
     */
    $more_pages = [
      "overview",
      "statistics",
    ];

    /**
     * @var ?string
     */
    $more = filter_var(GET->more ?? "overview", FILTER_SANITIZE_SPECIAL_CHARS);
    $sub_page = $is_sub_page ? $sub_page : (!in_array($more, $more_pages) ? "overview" : $more);

    /**
     * Get current rank
     */
    $rankings = $User->get_rankings($gumode);

    /**
     * @var Stat
     */
    $Stats = $User->stats()
      ->where("mode", $gumode)
      ->first();

    /**
     * If the current user is following this user, touch the
     * relationship instance to reset activity in the feed.
     */
    if (!$is_my_profile && LOGGED) {
      if ($CurrentUser->follows($User))
        $CurrentUser->followings()
          ->where("user2", $User->id)
          ->first()
          ->pivot
          ->update([
            "updated_at" => CURRENT_TIMESTAMP,
          ]);
    }

    /**
     * @var bool
     */
    $is_my_profile = $User->id === $CurrentUser->id;

    /**
     * @var Profile
     */
    $Profile = $User->profile ?? $User->create_profile();

    /**
     * @var string
     */
    $base_url = "/u/$User->id";

    /**
     * Add scores for our bot Aida.
     */
    if (1 === 2)
      include_once __DIR__ . "/Aida/_add_scores.php";

    /**
     * Header
     */
    include_once __DIR__ . "/_header.php";

    /**
     * Menu to show when on mobile.
     */
    include_once __DIR__ . "/_mobile_menu.php";

    /**
     * @var string
     */
    $file_path = __DIR__ . "/pages/_$sub_page.php";

    $Statss = $CurrentUser
      ->stats()
      ->selectRaw("mode, tscore, rscore, pp, acc")
      ->get();

    /**
     * Include the page of more, or fallback to the index.
     */
    include file_exists($file_path) ? $file_path : __DIR__ . "/pages/_index.php";

    /**
     * Page end with nice grey birdy.
     */
    include TEMPLATE . "/global/_scroll_end_logo.php";
  }
}
