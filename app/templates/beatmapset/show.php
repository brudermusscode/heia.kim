<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Artist;

/**
 * Get params
 */
$set_id = filter_var($_GET["set_id"] ?? 0, FILTER_VALIDATE_INT) ?? 0;
$map_id = filter_var($_GET["map_id"] ?? 0, FILTER_VALIDATE_INT) ?? 0;
$mode   = filter_var($_GET["mode"] ?? "osu", FILTER_SANITIZE_SPECIAL_CHARS);
$mod
  = $current_mod
  = filter_var($_GET["mod"] ?? "vanilla", FILTER_SANITIZE_SPECIAL_CHARS);

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

/**
 * Set and beatmap are set?
 */
if (!$Set || !$Beatmap)
  include UNAVAILABLE;
else {

  /**
   * Validate mode
   */
  if (!in_array($mode, Gamemode::$modes_text))
    $mode = Gamemode::$modes_text[0];

  /**
   * Validate mod
   */
  if (!in_array($mod, Gamemode::$mods_text))
    $mod = $current_mod = Gamemode::$mods_text[0];

  /**
   * Gumode
   */
  $gumode = Gamemode::find_gumode($mode, $mod, array: false);

  /**
   * All Beatmaps of this Set
   */
  $Beatmaps = $Set->beatmaps()
    ->with("feedback")
    ->orderByDesc("diff")
    ->get();

  /**
   * Create featured artists from map title and artist column.
   */
  $Beatmap->create_featured_artists();

  /**
   * @var Artist
   */
  $Artists = $Set->artists;

  /**
   * URLs
   */
  $base_url = "/beatmap-set/$Set->id/$Beatmap->id";

  /**
   * Include header
   */
  include __DIR__ . "/_header.php";

  /**
   * Show menu on mobile devices
   */
  include __DIR__ . "/_mobile_menu.php";

?>

  <div class=set__main content-width=wide fl fldircol content-gap>
    <div class="s__">
      <?php include __DIR__ . "/_difficulties.php"; ?>

      <div class="s__actions">
        <div fl align-items=center gap=smol>
          <div>
            <form data-form="feedback:create">
              <input type="hidden" name="type" value="beatmap">
              <input type="hidden" name="reference_id" value="<?= $Beatmap->id; ?>">
              <input type="hidden" name="action" value="thumb_up">
              <div has-count fl gap=smol+ alic>
                <div count>
                  <p text std bold><?= $Beatmap->feedback()->count(); ?></p>
                </div>
                <mbutton mid submit-closest icon-only has-tooltip=bottom filled=lighter ripple-effect
                  <?php if (LOGGED && CurrentUser->favorite_beatmaps()->where("reference_id",  $Beatmap->id)->first()) echo "active"; ?>>
                  <mi>kid_star</mi>
                  <div ttooltip>
                    <p text std bold><?= __("Add to favorites") ?></p>
                  </div>
                </mbutton>
              </div>
            </form>
          </div>

          <?php

          /**
           * Check the INFO_WINDOWS cookie to contain the string
           * `beatmap_comments` and hide it, if it is in.
           */
          $show_info_window = !in_array("beatmap_comments", INFO_WINDOWS);

          ?>

          <div has-tooltip=bottom <?php if ($show_info_window) echo "has-info-window"; ?> disabled>
            <mbutton mid data-action="beatmaps:comments,open" data-id=<?= $Set->id; ?> icon-only filled=lighter ripple-effect>
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
              <mbutton mid outlined icon-only ripple-effect open-more-menu has-tooltip=bottom>
                <div notification-dot></div>
                <mi>more_vert</mi>
                <div ttooltip>
                  <p text std bold><?= __("More options") ?></p>
                </div>
              </mbutton>

              <jump-menu menu-more filled=lighter elevated color=dynamic>


                <!--- REQUEST --->
                <?php

                /**
                 * @var bool
                 */
                $can_request_ranking = $Beatmap->ranking_requestable();

                if ($can_request_ranking) :
                  $MyBeatmapRequest = CurrentUser->beatmap_requests()
                    ->where("map_id", $Beatmap->id)
                    ->where("active", 1)
                    ->first();

                  if (!$MyBeatmapRequest) :

                ?>
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
                <?php if (CurrentUser->squad) { ?>
                  <div dno ripple-effect class=jm__option hoverable
                    request-get="squad:thread:new"
                    data-id=<?= $Set->id; ?>
                    data-type=beatmap>
                    <mi>gesture</mi>
                    <p text std><?= __("Create Squad Thread") ?></p>
                  </div>
                <?php } else { ?>
                  <a href="/squads">
                    <div ripple-effect class=jm__option hoverable>
                      <mi>workspaces</mi>
                      <p text std><?= __("Join a Squad for more") ?></p>
                    </div>
                  </a>
                <?php }

                // TODO: Create squad post for beatmaps.
                ?>

                <div
                  request-get="ui:squad:posting-machine"
                  data-type="squad:post"
                  data-sub-type="text"
                  data-attachment-id=<?= $Set->id; ?>
                  data-attachment-type="beatmap:set"
                  ripple-effect class=jm__option hoverable>
                  <mi>post_add</mi>
                  <p text std>Post to Squad</p>
                </div>
              </jump-menu>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <?php

    /**
     * Scores
     */
    $Scores = $Beatmap->score_leaderboard($gumode, limit: 10);

    ?>

    <div class="s__content">

      <!--- SCORES --->
      <div class="s__scores" flexone fl fldircol gap=smol>
        <?php if ($Scores->count()) { ?>
          <div title-inline="">
            <p text mid bold><?= __("Leaderboard") ?></p>
          </div>
        <?php } ?>

        <?php if (!$Scores->count()) { ?>

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

          /**
           * Only show the very first element of object.
           */
          foreach ($Scores->take(1) as $key => $Score)
            include __DIR__ . "/_user_card.php";

        ?>

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

        <?php

          } else {
            foreach ($Scores->skip(1) as $key => $Score)
              include __DIR__ . "/_user_card.php";

            unset($rank);
          }
        }

        ?>
      </div>



      <!--- STATS --->
      <div class=s__stats fl fldircol gap=smol+>
        <div title-inline="">
          <p text mid bold><?= __("Statistics") ?></p>
        </div>
        <?php include __DIR__ . "/_stats.php"; ?>
      </div>
    </div>

    <?php

    /**
     * Build an array with artist's names
     */
    $artist_names = [];

    if ($Artists->count())
      foreach ($Artists as $Artist)
        array_push($artist_names, $Artist->name);

    /**
     * @var int
     */
    $fetch_count = 8;

    /**
     * @var ?Set
     */
    $Sets = Beatmap\Set::view(
      query: implode(" ", $artist_names),
      mode: Gamemode::mode_int($mode),
      limit: $fetch_count,
    );

    /**
     * @var int
     */
    $count = $Sets->count();

    if ($Sets->count() && $Artists->count()) {

    ?>

      <div class="s__maps" fl fldircol gap>
        <div pblock12>
          <p text midler bold>
            <?php

            $featured_artist_count = $Artists->count();
            $artist_str = __("More from") . " " . $Artists->first()->name;

            if ($featured_artist_count > 1)
              foreach ($Artists as $kkey => $Artist) {
                if ($kkey == 0)
                  continue;

                if (($featured_artist_count - $kkey) == 1)
                  $artist_str .= " and $Artist->name";
                else
                  $artist_str .= ", $Artist->name";
              }

            echo $artist_str;

            ?>
          </p>
        </div>

        <div grid-repeat gap="smol">
          <?php

          $include_all_diffs = true;

          foreach ($Sets->take(6) as $key => $Set)
            include TEMPLATE . "/beatmapset/_set-card.php";

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

    <?php } ?>
  </div>


<?php

  include TEMPLATE . "/global/_scroll_end_logo.php";
}
