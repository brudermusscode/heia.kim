<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/init.php";

use Heiakim\Application\Application;
use Heiakim\Http\Request;
use Heiakim\Http\CSRF;
use Heiakim\Model\User;
use Heiakim\Time\Time;

/**
 * @var User $CurrentUser
 */

$__RETURN = new Request;
$__DD = Application::get_default_dialogues();
$error_url = dirname(__DIR__) . "/app/templates/errors/invalid_request.php";

/**
 * @var string CSRF Token sent with the headerts to be validated.
 */
$__CSRF_TOKEN = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

/**
 * Include all router files and the basic router confirguration.
 */
include_once CONFIG . "/router.php";

/**
 * Include opengraph elements.
 */
include_once CONFIG . "/opengraph.php";

/**
 * @var object
 */
$template_return = (object) [];

ob_start();
include_once $include_template;

$template_return->include_template = ob_get_clean();


/**
 * Prepare the template to be included.
 */
$template_return->include_template = Application::replace_braced_variables(
  $template_return->include_template,
  [
    "APP_NAME" => APP_NAME,
    "PREMIUM_NAME" => PREMIUM_NAME,
    "DISCORD" => _env("DISCORD_INVITE"),
    "LOGGED" => LOGGED,
    "CurrentUser" => $CurrentUser,
  ],
);

/**
 * Set the new title.
 */
$template_return->title = $title;

exit(json_encode($template_return));




$userData = [
  "avatar_url" => "https://a.ppy.sh/25367973?1708967366.jpeg",
  "country_code" => "DE",
  "default_group" => "default",
  "id" => 25367973,
  "is_active" => true,
  "is_bot" => false,
  "is_deleted" => false,
  "is_online" => true,
  "is_supporter" => false,
  "last_visit" => "2024-02-26T18:09:23+00:00",
  "pm_friends_only" => false,
  "profile_colour" => null,
  "username" => "heia kim",
  "cover_url" => "https://assets.ppy.sh/user-profile-covers/25367973/b174d9c35c6df268e79e65f8e2e20540a8aa2a08e68ffe03095301d68e32885f.jpeg",
  "discord" => null,
  "has_supported" => true,
  "interests" => null,
  "join_date" => "2021-08-16T13:34:35+00:00",
  "location" => null,
  "max_blocks" => 100,
  "max_friends" => 500,
  "occupation" => null,
  "playmode" => "osu",
  "playstyle" => ["keyboard", "tablet"],
  "post_count" => 0,
  "profile_order" => ["me", "top_ranks", "recent_activity", "historical", "medals", "beatmaps", "kudosu"],
  "title" => null,
  "title_url" => null,
  "twitter" => null,
  "website" => "https://www.heia.kim",
  "country" => [
    "code" => "DE",
    "name" => "Germany"
  ],
  "cover" => [
    "custom_url" => "https://assets.ppy.sh/user-profile-covers/25367973/b174d9c35c6df268e79e65f8e2e20540a8aa2a08e68ffe03095301d68e32885f.jpeg",
    "url" => "https://assets.ppy.sh/user-profile-covers/25367973/b174d9c35c6df268e79e65f8e2e20540a8aa2a08e68ffe03095301d68e32885f.jpeg",
    "id" => null
  ],
  "is_restricted" => false,
  "kudosu" => [
    "available" => 0,
    "total" => 0
  ],
  "account_history" => [],
  "active_tournament_banner" => null,
  "active_tournament_banners" => [],
  "badges" => [],
  "beatmap_playcounts_count" => 964,
  "comments_count" => 1,
  "favourite_beatmapset_count" => 1,
  "follower_count" => 7,
  "graveyard_beatmapset_count" => 0,
  "groups" => [],
  "guest_beatmapset_count" => 0,
  "loved_beatmapset_count" => 0,
  "mapping_follower_count" => 0,
  "monthly_playcounts" => [
    ["start_date" => "2021-08-01", "count" => 705],
    ["start_date" => "2021-09-01", "count" => 625],
    ["start_date" => "2021-10-01", "count" => 106],
    ["start_date" => "2021-11-01", "count" => 167],
    ["start_date" => "2021-12-01", "count" => 71],
    ["start_date" => "2022-01-01", "count" => 229],
    ["start_date" => "2022-02-01", "count" => 180],
    ["start_date" => "2022-03-01", "count" => 56],
    ["start_date" => "2022-04-01", "count" => 26],
    ["start_date" => "2022-05-01", "count" => 33],
    ["start_date" => "2022-06-01", "count" => 15],
    ["start_date" => "2022-07-01", "count" => 434],
    ["start_date" => "2022-08-01", "count" => 1],
    ["start_date" => "2023-01-01", "count" => 8],
    ["start_date" => "2023-03-01", "count" => 15],
    ["start_date" => "2023-06-01", "count" => 59]
  ],
  "nominated_beatmapset_count" => 0,
  "page" => [
    "html" => "",
    "raw" => ""
  ],
  "pending_beatmapset_count" => 0,
  "previous_usernames" => ["Flasche Luft", "Pantyhoses"],
  "rank_highest" => ["rank" => 213172, "updated_at" => "2022-07-26T20:33:45Z"],
  "ranked_beatmapset_count" => 0,
  "replays_watched_counts" => [],
  "scores_best_count" => 100,
  "scores_first_count" => 0,
  "scores_pinned_count" => 0,
  "scores_recent_count" => 0,
  "session_verified" => true,
  "statistics" => [
    "count_100" => 108189,
    "count_300" => 793032,
    "count_50" => 10593,
    "count_miss" => 37105,
    "level" => ["current" => 87, "progress" => 55],
    "global_rank" => 246404,
    "global_rank_exp" => null,
    "pp" => 2825.34,
    "pp_exp" => 0,
    "ranked_score" => 1691105762,
    "hit_accuracy" => 97.5248,
    "play_count" => 2728,
    "play_time" => 234445,
    "total_score" => 4450675545,
    "total_hits" => 911814,
    "maximum_combo" => 1400,
    "replays_watched_by_others" => 0,
    "is_ranked" => true,
    "grade_counts" => ["ss" => 5, "ssh" => 0, "s" => 124, "sh" => 0, "a" => 187],
    "country_rank" => 12964,
    "rank" => ["country" => 12964]
  ],
  "statistics_rulesets" => [
    "osu" => [
      "count_100" => 108189,
      "count_300" => 793032,
      "count_50" => 10593,
      "count_miss" => 37105,
      "level" => ["current" => 87, "progress" => 55],
      "global_rank" => 246404,
      "global_rank_exp" => null,
      "pp" => 2825.34,
      "pp_exp" => 0,
      "ranked_score" => 1691105762,
      "hit_accuracy" => 97.5248,
      "play_count" => 2728,
      "play_time" => 234445,
      "total_score" => 4450675545,
      "total_hits" => 911814,
      "maximum_combo" => 1400,
      "replays_watched_by_others" => 0,
      "is_ranked" => true,
      "grade_counts" => ["ss" => 5, "ssh" => 0, "s" => 124, "sh" => 0, "a" => 187]
    ],
    "mania" => [
      "count_100" => 88,
      "count_300" => 164,
      "count_50" => 23,
      "count_miss" => 22,
      "level" => ["current" => 5, "progress" => 4],
      "global_rank" => 2485673,
      "global_rank_exp" => null,
      "pp" => 0.25,
      "pp_exp" => 0,
      "ranked_score" => 380657,
      "hit_accuracy" => 69.224,
      "play_count" => 2,
      "play_time" => 138,
      "total_score" => 722619,
      "total_hits" => 275,
      "maximum_combo" => 113,
      "replays_watched_by_others" => 0,
      "is_ranked" => true,
      "grade_counts" => ["ss" => 0, "ssh" => 0, "s" => 0, "sh" => 0, "a" => 0]
    ]
  ],
  "support_level" => 0,
  "user_achievements" => [
    ["achieved_at" => "2022-07-25T21:42:34Z", "achievement_id" => 152],
    ["achieved_at" => "2022-07-25T21:38:10Z", "achievement_id" => 40],
    ["achieved_at" => "2022-07-25T13:01:45Z", "achievement_id" => 124],
    ["achieved_at" => "2021-12-09T16:00:59Z", "achievement_id" => 67],
    ["achieved_at" => "2021-11-27T00:37:58Z", "achievement_id" => 87],
    ["achieved_at" => "2021-11-27T00:37:58Z", "achievement_id" => 54],
    ["achieved_at" => "2021-10-09T17:15:10Z", "achievement_id" => 176],
    ["achieved_at" => "2021-09-17T22:10:35Z", "achievement_id" => 126],
    ["achieved_at" => "2021-09-17T21:50:53Z", "achievement_id" => 121],
    ["achieved_at" => "2021-09-12T07:08:02Z", "achievement_id" => 122],
    ["achieved_at" => "2021-09-12T04:30:08Z", "achievement_id" => 39],
    ["achieved_at" => "2021-09-12T03:00:13Z", "achievement_id" => 63],
    ["achieved_at" => "2021-09-04T06:11:50Z", "achievement_id" => 60],
    ["achieved_at" => "2021-09-03T21:54:29Z", "achievement_id" => 148],
    ["achieved_at" => "2021-08-28T16:02:24Z", "achievement_id" => 127],
    ["achieved_at" => "2021-08-23T00:50:24Z", "achievement_id" => 59],
    ["achieved_at" => "2021-08-21T02:52:34Z", "achievement_id" => 4],
    ["achieved_at" => "2021-08-20T13:47:22Z", "achievement_id" => 66],
    ["achieved_at" => "2021-08-20T00:42:32Z", "achievement_id" => 3],
    ["achieved_at" => "2021-08-19T23:08:23Z", "achievement_id" => 15],
    ["achieved_at" => "2021-08-19T23:02:47Z", "achievement_id" => 65],
    ["achieved_at" => "2021-08-19T13:44:19Z", "achievement_id" => 58],
    ["achieved_at" => "2021-08-19T13:17:06Z", "achievement_id" => 131],
    ["achieved_at" => "2021-08-17T08:42:27Z", "achievement_id" => 64],
    ["achieved_at" => "2021-08-16T22:19:38Z", "achievement_id" => 57],
    ["achieved_at" => "2021-08-16T21:04:38Z", "achievement_id" => 1],
    ["achieved_at" => "2021-08-16T19:35:33Z", "achievement_id" => 56],
    ["achieved_at" => "2021-08-16T13:39:07Z", "achievement_id" => 55]
  ],
  "rank_history" => [
    "mode" => "osu",
    "data" => [
      238443,
      238531,
      238610,
      238696,
      238777,
      238857,
      238937,
      239000,
      239076,
      239145,
      239212,
      239288,
      239377,
      239461,
      239529,
      239594,
      239657,
      239729,
      239844,
      239932,
      240007,
      240052,
      240138,
      240219,
      239751,
      239820,
      239893,
      239976,
      240070,
      240143,
      240205,
      240301,
      240417,
      240494,
      240543,
      240623,
      240702,
      240861,
      240956,
      241030,
      241082,
      241153,
      241224,
      241283,
      241375,
      241468,
      241546,
      241621,
      241705,
      241783,
      241872,
      241970,
      242049,
      242140,
      242191,
      242278,
      242354,
      242449,
      242528,
      242610,
      242696,
      242741,
      243178,
      243456,
      243622,
      243784,
      243962,
      244118,
      244243,
      244369,
      244490,
      244561,
      244695,
      244813,
      244928,
      245017,
      245123,
      245217,
      245313,
      245432,
      245566,
      245680,
      245799,
      245890,
      245969,
      246061,
      246195,
      246301,
      246404,
      246404
    ]
  ],
  "ranked_and_approved_beatmapset_count" => 0,
  "unranked_beatmapset_count" => 0
];
