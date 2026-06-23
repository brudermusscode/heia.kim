<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Artist;
use Heiakim\Model\Feedback;
use Heiakim\Model\Score;
use Illuminate\Support\Collection;

$set_id = aglobal("set_id") ?? 0;
$map_id = aglobal("map_id") ?? 0;
$mode   = aglobal("mode") ?? "osu";
$mod    = $current_mod = aglobal("mod") ?? "vanilla";

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

redirect_unauthorized($Beatmap, $Set);

# Serialize mode.
if (!in_array($mode, Gamemode::$modes_text))
  $mode = Gamemode::$modes_text[0];

# Serialize mod.
if (!in_array($mod, Gamemode::$mods_text))
  $mod = $current_mod = Gamemode::$mods_text[0];

# Ensure the modification is valid for the current ruleset.
$mod = Gamemode::validate_mod($mod, $mode);

$valid_mods = Gamemode::valid_mods($mode);

$gumode = Gamemode::find_gumode($mode, $mod, array: false);

/**
 * @var Collection<Beatmap>
 */
$Beatmaps = $Set->beatmaps()
  ->with("feedback")
  ->orderByDesc("diff")
  ->get();

# This will ensure that the artist string is split by certain characters to create
# new Artists and attach them to this Beatmap/Set.
$Beatmap->create_featured_artists();

/**
 * @var Collection<Artist>
 */
$Artists = $Set->artists;

$base_url = "/beatmap-set/$Set->id/$Beatmap->id";

# Partial inclusion.
include TEMPLATE . "/home/_page-navigator.php";
include __DIR__ . "/_header.php";
include COMPONENT . "/_mode-menu.php";
include __DIR__ . "/_mobile-menu.php"; ?>

<content beatmapset widest minlineauto fl fldircol gap=std+>
  <div class="s__">

    <?php include __DIR__ . "/_difficulties.php"; ?>

    <div class="s__actions">
      <div fl alic gap=smol>
        <?php

        /**
         * @var ?Feedback
         */
        $Feedback = CurrentUser->favorite_beatmaps()
          ->where("reference_id",  $Beatmap->id)
          ->first();

        $likes_this = LOGGED && $Feedback;

        ?>

        <mbutton mid icon-only has-tooltip=bottom filled=lighter
          ripple-effect shadow-submit reload responder=error
          request="feedback:<?= $likes_this ? "delete" : "create" ?>"
          <?php if ($likes_this) : ?>
          data-id="<?= $Feedback->id ?>"
          <?php else : ?>
          data-type="beatmap"
          data-reference-id="<?= $Beatmap->id; ?>"
          data-action="thumb_up"
          <?php endif; ?>
          <?= $likes_this ? "active" : "" ?>>
          <mi>bookmark_heart</mi>
          <div ttooltip>
            <p text std bold><?= __(($likes_this ? "Remove from" : "Add to") . " favorites") ?></p>
          </div>
        </mbutton>

        <?php

        $show_info_window = !in_array("beatmap_comments", INFO_WINDOWS);

        ?>

        <div has-tooltip=bottom disabled
          <?= $show_info_window ? "has-info-window" : ""; ?>>
          <mbutton mid icon-only filled=lighter ripple-effect
            data-action="beatmap:comment:wrapper"
            data-id=<?= $Set->id; ?>>
            <mi>forum</mi>
          </mbutton>

          <div ttooltip>
            <p text std bold><?= __("Comments") ?></p>
          </div>

          <div class=info_window style="top:calc(100% + .2em);" dno>
            <div class=pulse rounded=std></div>
            <box-model background=invert color=invert elevated=wide data-action="beatmaps:comments,open"
              data-id=<?= $Set->id; ?> has-only size=std rounded=std material ripple-effect>
              <div
                style="position:absolute;height:.6em;width:.6em;top:0;left:50%;translate:-50% 0;rotate:45deg;margin-top:-.3em;"
                background=invert></div>
              <div pblock14 pinline10>
                <div text std no-line-break>Press
                  <p style=display:inline-block;margin-inline:.12em; filled=darker color=dynamic rounded=smol pblock8 pinline4
                    text smol bold>C
                  </p> to open comments
                </div>
              </div>
            </box-model>
          </div>
        </div>

        <a extern href="<?= _env("BEATMAP_MIRROR") . "/api/d/$Set->id"; ?>">
          <mbutton mid background=slight-green color=dark-green icon-only ripple-effect has-tooltip=bottom>
            <mi>arrow_downward</mi>
            <div ttooltip>
              <p text std bold><?= __("Download") ?></p>
            </div>
          </mbutton>
        </a>

        <?php if (LOGGED) { ?>
          <div posrel menu-outer>
            <mbutton open-more-menu
              mid outlined icon-only ripple-effect has-tooltip=bottom>
              <div notification-dot></div>
              <mi>more_vert</mi>
              <div ttooltip>
                <p text std bold><?= __("More options") ?></p>
              </div>
            </mbutton>

            <jump-menu menu-more filled=lighter elevated>
              <!--- REQUEST --->
              <?php

              $can_request_ranking = $Beatmap->ranking_requestable();

              if ($can_request_ranking) :
                $MyBeatmapRequest = CurrentUser->beatmap_requests()
                  ->where("map_id", $Beatmap->id)
                  ->where("active", 1)
                  ->first();

                if (!$MyBeatmapRequest) : ?>
                  <div request-get="request:new" data-id="<?= $Beatmap->id; ?>" ripple-effect
                    class=jm__option hoverable>
                    <mi>forward</mi>
                    <p text std>Request ranking</p>
                  </div>
                <?php else : ?>
                  <div disabled class=jm__option hoverable>
                    <mi class="loader-pulse" posrel style="height:24px;width:24px;top:0;">
                      <span></span>
                    </mi>
                    <p text std>Rank-Request pending</p>
                  </div>
                <?php endif; ?>

                <div divide="line"></div>
              <?php endif; ?>


              <!--- SQUAD --->
              <?php if (CurrentUser->squad) : ?>
                <div dno ripple-effect class=jm__option hoverable
                  request-get="squad:thread:new"
                  data-id=<?= $Set->id; ?>
                  data-type=beatmap>
                  <mi>gesture</mi>
                  <p text std><?= __("Create Squad Thread") ?></p>
                </div>
              <?php else : ?>
                <a href="/squads">
                  <div ripple-effect class=jm__option hoverable>
                    <mi>workspaces</mi>
                    <p text std><?= __("Join a Squad for more") ?></p>
                  </div>
                </a>
              <?php endif;

              // TODO: Create squad post for beatmaps.
              ?>

              <div ripple-effect class=jm__option hoverable
                request-get="ui:posting-machine"
                data-type="squad:post"
                data-sub-type="text"
                data-attachment-id="<?= $Set->id; ?>"
                data-attachment-type="beatmap:set">
                <mi>post_add</mi>
                <p text>Post to Squad</p>
              </div>
            </jump-menu>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <?php

  /**
   * @var Collection<Score>
   */
  $Scores = $Beatmap->score_leaderboard($gumode, limit: 10);

  ?>

  <div class="s__content">

    <!--- SCORES --->
    <div class="s__scores" flexone fl fldircol gap=smol+>
      <p text bold ttup title-inline><?= __("Leaderboard") ?></p>

      <?php

      # When there are no scores.
      if (!$Scores->count()) { ?>
        <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap style="flex:1;">
          <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
            <i class="mi" size="wide">emoji_events</i>
          </div>
          <div tac>
            <p text bold wide><?= __("No scores") ?></p>
            <p text std><?= __("Be the first one to set an amazing score!") ?></p>
          </div>
          <div fl justify-content="center">
            <a extern href="<?= _env("BEATMAP_MIRROR") . "/api/d/$set_id"; ?>">
              <mbutton mid has-icon=left background=follow color=dark-green>
                <mi>arrow_downward</mi>
                <p text bold><?= __("Download") ?></p>
              </mbutton>
            </a>
          </div>
        </box-model>

      <?php

      } else {

        # Only show the very first User from top scores.
        foreach ($Scores->take(1) as $key => $Score)
          include __DIR__ . "/_user_card.php"; ?>

        <div class="divide"></div>

        <?php if ($Scores->count() < 2) { ?>
          <box-model outlined animation=fade-in>
            <bm-inr size=mid>
              <div fl alic gap>
                <mi size=mid>task_alt</mi>
                <p text std><?= __("That's all!") ?></p>
              </div>
            </bm-inr>
          </box-model>
      <?php } else {
          foreach ($Scores->skip(1) as $key => $Score)
            include __DIR__ . "/_user_card.php";

          unset($rank);
        }
      } ?>
    </div>

    <!--- STATS --->
    <div class=s__stats fl fldircol gap=smol+>
      <p text bold ttup title-inline><?= __("Statistics") ?></p>
      <?php include __DIR__ . "/_stats.php"; ?>
    </div>
  </div>

  <?php

  $artist_names = [];

  if ($Artists->count())
    foreach ($Artists as $Artist)
      array_push($artist_names, $Artist->name);

  $fetch_count = 8;

  /**
   * @var ?Collection<Set>
   */
  $Sets = Beatmap\Set::view(
    query: implode(" ", $artist_names),
    mode: Gamemode::mode_int($mode),
    limit: $fetch_count,
  );

  $count = $Sets->count();

  if ($Sets->count() && $Artists->count()) : ?>
    <div class="s__maps" fl fldircol gap=smol+>
      <p text ttup bold title-inline>
        <?php

        $featured_artist_count = $Artists->count();
        $artist_str = __("More from") . " " . $Artists->first()->name;

        if ($featured_artist_count > 1)
          foreach ($Artists as $kkey => $Artist) :
            /**
             * @var int $kkey
             */

            if ($kkey == 0)
              continue;

            if (($featured_artist_count - $kkey) == 1)
              $artist_str .= " and $Artist->name";
            else
              $artist_str .= ", $Artist->name";
          endforeach;

        echo $artist_str; ?>
      </p>

      <div grid-repeat gap="smol">
        <?php

        $include_all_diffs = true;

        foreach ($Sets->take(6) as $key => $Set)
          include TEMPLATE . "/beatmap/_beatmap-row.php";

        ?>
      </div>

      <?php if ($Artists->count()) { ?>
        <div mt=smol fl justify-content=center>
          <a href="<?= "/artist/" . $Artists->first()->id; ?>">
            <mbutton ripple-effect filled=lighter has-icon=right>
              <p text smol bold ttup><?= __("More beatmaps") ?></p>
              <mi>east</mi>
            </mbutton>
          </a>
        </div>
      <?php } ?>
    </div>
  <?php endif; ?>
  </div>

  <?php include TEMPLATE . "/global/_scroll_end_logo.php";
