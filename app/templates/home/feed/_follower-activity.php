<?php

use Heiakim\Time\Time;

?>

<div fl fldircol gap=smol+>
  <div fl fldircol jucc style="height:46px;">
    <p text bold ttup>Players you follow</p>
    <p text smol color=company>Newest activity of players you follow</p>
  </div>

  <div fl fldircol gap=smoler>
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
      <a href="<?= $Follower->link() ?>">
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
      </a>
    <?php } ?>
  </div>
</div>