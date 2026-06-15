<?php

use Heiakim\Model\Gamemode;
use Heiakim\Time\Time;

/**
 * Serialize get
 */
$sub = filter_input(INPUT_GET, "sub", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Favorite mode based on the amount of plays set inside a single
 * gamemode which is defined by it's gulag mode.
 *
 * @var array
 */
$favorite_modes = CurrentUser->favorite_modes();
$favorite_gumode = $favorite_modes[0]->mode ?? null;

/**
 * @var object
 */
$favorite_mode = $favorite_gumode === null ? null : Gamemode::gumode_text($favorite_gumode);

/**
 * Include main page navigator.
 */
include PAGE_NAVIGATOR;

?>

<feed-sidebar dno>
  <div class="fs__bottom_fade"></div>

  <div class=feed_sidebar__inr>
    <div fl fldircol gap=smol>
      <div class=fs__label>
        <div pblock4>
          <p text smol bold ttup timestamp><?= __("media") ?></p>
        </div>
      </div>

      <a href="/home" sub>
        <mbutton mid icon-only background=slighter <?php if (!$sub) echo "active"; ?> has-tooltip=right>
          <mi>browse</mi>
          <div ttooltip>
            <p text std bold>Feed - <?= __("What's new?") ?></p>
          </div>
        </mbutton>
      </a>

      <a href="/home/beatmaps" sub>
        <mbutton mid icon-only background=slighter <?php if ($sub == "beatmaps") echo "active"; ?> has-tooltip=right>
          <mi>web_stories</mi>
          <div ttooltip>
            <p text std bold><?= __("Favorite") ?> Beatmaps</p>
          </div>
        </mbutton>
      </a>

      <a href="/home/artists" sub>
        <mbutton mid icon-only background=slighter <?php if ($sub == "artists") echo "active"; ?> has-tooltip=right>
          <mi>stars</mi>
          <div ttooltip>
            <p text std bold><?= __("Starred") ?> <?= __("Artists") ?></p>
          </div>
        </mbutton>
      </a>
    </div>

    <div fl fldircol gap=smol align-items=center posrel>
      <div class=fs__label>
        <div pblock4>
          <p text smol bold ttup timestamp><?= __("activity") ?></p>
        </div>
      </div>

      <?php

      /**
       * Following
       */
      $Following = CurrentUser->followings;

      /**
       * Sort the followed people by latest activity.
       */
      $SortedFollowing = $Following->sortByDesc(function ($Follower) {
        $last_activity = date("Y-m-d H:i:s", $Follower->latest_activity);
        $last_activity_checked = $Follower->pivot->updated_at;

        if ($Follower->pivot->updated_at == null)
          return $Follower->pivot->created_at < $last_activity;

        return $last_activity > $last_activity_checked;
      });

      foreach ($SortedFollowing->take(10) ?? [] as $Follower) {
        /**
         * Activity
         */
        $last_activity_timestamp = date("Y-m-d H:i:s", $Follower->latest_activity);
        $last_activity_ago = Time::ago($last_activity_timestamp);

        /**
         * Compare the timestamp when the current user last
         * visited the followers profile and the timestamp from
         * the followers last activity. Based on this, we know if
         * the followed user has new activity to share with the
         * current user.
         */
        $last_activity_checked = $Follower->pivot->updated_at;
        $relationship_created_at = $Follower->pivot->created_at;

        $has_activity =
          $last_activity_checked == NULL
          ? $relationship_created_at < $last_activity_timestamp
          : $last_activity_checked < $last_activity_timestamp;

      ?>

        <div class=fs__user <?= $has_activity ? "active" : ""; ?> data-action="users:hover-card" data-id="<?= $Follower->id; ?>" has-hover-card>
          <a href="/u/<?= $Follower->id; ?>">
            <div class=fs__user_inr>
              <div class=fs__user_image>
                <picture size=mid circled>
                  <?php $Follower->image(); ?>
                </picture>
              </div>
            </div>
          </a>
          <?php if ($has_activity) { ?>
            <div class=bg-wrap>
              <?php include ROOT . "/public/assets/images/fancy-colorful-bg.html"; ?>
            </div>
          <?php } ?>

          <div class="activity-status" data-react="users:hover-card"></div>
        </div>

      <?php } ?>

      <a href="/leaderboard">
        <mbutton mid icon-only outlined has-tooltip=right>
          <mi>add</mi>
          <div ttooltip>
            <p text std bold>Explore</p>
          </div>
        </mbutton>
      </a>

    </div>
  </div>
</feed-sidebar>

<feed>
  <?php

  switch ($sub) {
    case "beatmaps":
      include __DIR__ . "/feed/_beatmaps.php";
      break;

    case "artists":
      include __DIR__ . "/feed/_artists.php";
      break;

    default:
      include __DIR__ . "/feed/_index.php";
  }

  ?>

  <div style=width:100%;>
    <?php

    /**
     * Page end with nice grey birdy.
     */
    include TEMPLATE . "/global/_scroll_end_logo.php";

    ?>
  </div>
</feed>