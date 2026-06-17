<?php

use Heiakim\Model\Feed;
use Heiakim\Time\Time;

/**
 * @var string $sub
 */

$base_limit = 3;
$Feed = new Feed(CurrentUser);

?>

<?php if (!in_array("applications_open", INFO_WINDOWS)) { ?>
  <feed-section dialogue dno>
    <story-banner fl alic background=company color=light rounded=mid>
      <picture style=width:24em;margin-bottom:-2em;margin-top:-2em;>
        <img src="<?= IMAGE . "/legal/apply.svg"; ?>" />
      </picture>

      <div fl gap fldircol>
        <div fl fldircol gap=smol>
          <p style="line-height:.9;" text wide bold>
            <?= __("Want to work with us?") ?></p>
          <p text>
            <?= __("Applications for <strong>Moderators</strong>, <strong>Assistants</strong> and <strong>Beatmap Nominators</strong> are open.") ?>
          </p>
        </div>

        <div fl jucend>
          <a href="/legal/applications">
            <mbutton background=invert color=invert>
              <p text bold><?= __("Apply now") ?></p>
            </mbutton>
          </a>
        </div>
      </div>

      <mbutton close hoverable icon-only close-dialogue
        data-info-window="applications_open">
        <mi size=midler>close</mi>
      </mbutton>
    </story-banner>
  </feed-section>
<?php } ?>

<div fl alistart jucstretch gap>
  <column-wrapper smol>
  </column-wrapper>

  <column-wrapper wide flone fl fldircol gap=wide flex-truncate>
    <!--- Newly Ranked --->
    <div fl fldircol gap=smol+>
      <div title-inline fl alic gap=smol+>
        <mbutton icon-only outlined text midler>🏅</mbutton>
        <div fl fldircol>
          <h2 text ttup><?= __("Newly ranked") ?></h2>
          <p text color=company>Beatmaps you can achieve performance on</p>
        </div>
      </div>

      <div fl fldircol gap=smol>
        <get-content from="/home/get-content/newly-ranked" fl fldircol gap=smol>
          <?php include TEMPLATE . "/beatmap/_placeholder-column.php" ?>
        </get-content>
      </div>
    </div>

    <!--- Newly Loved --->
    <div fl fldircol gap=smol+>
      <div title-inline fl alic gap=smol+>
        <mbutton icon-only outlined text midler>❤️</mbutton>
        <div>
          <h2 text ttup><?= __("Newly loved") ?></h2>
          <p text color=company>Beatmaps people like alot</p>
        </div>
      </div>
      <div fl fldircol gap=smol>
        <get-content from="/home/get-content/newly-loved" fl fldircol gap=smol>
          <?php include TEMPLATE . "/beatmap/_placeholder-column.php" ?>
        </get-content>
      </div>
    </div>

    <!--- Most Played --->
    <div fl fldircol gap=smol+>
      <div title-inline fl alic gap=smol+>
        <mbutton icon-only outlined text>
          <mi midler>trending_up</mi>
        </mbutton>
        <div>
          <h2 text ttup><?= __("Most played") ?></h2>
          <p text color=company>Beatmaps that have been played alot</p>
        </div>
      </div>
      <div fl fldircol gap=smol>
        <get-content from="/home/get-content/most-played-beatmaps" fl fldircol gap=smol>
          <?php include TEMPLATE . "/beatmap/_placeholder-column.php" ?>
        </get-content>
      </div>
    </div>
  </column-wrapper>

  <column-wrapper smol hide-tablet style="position:sticky;top:16px;">
    <div fl fldircol gap=smol+>
      <div fl fldircol jucc style="height:46px;">
        <p text bold ttup>Player you follow</p>
        <p text smol color=company>Activity of player you follow</p>
      </div>

      <?php

      $Following = CurrentUser->followings;

      # Sort the Followings by latest activity.
      $SortedFollowing = $Following->sortByDesc(function ($Follower) {
        $last_activity = date("Y-m-d H:i:s", $Follower->latest_activity);
        $last_activity_checked = $Follower->pivot->updated_at;

        if ($Follower->pivot->updated_at == null)
          return $Follower->pivot->created_at < $last_activity;

        return $last_activity > $last_activity_checked;
      });

      foreach ($SortedFollowing->take(6) ?? [] as $Follower) {
        $last_activity_timestamp = date("Y-m-d H:i:s", $Follower->latest_activity);
        $last_activity_ago = Time::ago($last_activity_timestamp);
        $last_activity_checked = $Follower->pivot->updated_at;
        $relationship_created_at = $Follower->pivot->created_at;

        $has_activity =
          $last_activity_checked == NULL
          ? $relationship_created_at < $last_activity_timestamp
          : $last_activity_checked < $last_activity_timestamp;

      ?>

        <div fl alic gap=smol p4 rounded=wide hoverable>
          <picture std circled>
            <?php $Follower->image(); ?>
            <?php if ($has_activity) { ?>
              <div class=bg-wrap>
                <?php
                include ROOT . "/public/assets/images/fancy-colorful-bg.html"; ?>
              </div>
            <?php } ?>
          </picture>

          <div>
            <p text bold><?= $Follower->name; ?></p>
            <p text smol>Last active &middot; <span color=company>
                <?= Time::ago($last_activity_timestamp, true); ?></span></p>
          </div>
        </div>

      <?php } ?>
    </div>
  </column-wrapper>
</div>