<?php

// TODO: Shows +0 more followers

use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Squad;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub_page
 * @var string $more
 * @var string $current_mod
 * @var string $mode
 * @var string $mod
 * @var int $gumode
 * @var bool $is_champion
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var bool $both_sides_can_interact_socially
 */

/**
 * Followers
 */
$Followers = $User->followers;
$followers_count = $Followers->count();

/**
 * The count to show followers.
 */
$show_followers_count = 4;
$show_followers_circle_count = 7;
$followers_shown = 0;
$followers_text = __("Followers");
$followers_text_slogan = __("Nobody has decided to follow you by now. What a shame!");

$no_followers_my = <<<TEXT
<box-model outlined mb=std>
  <div pblock48 pinline48>
    <div fl fldircol gap>
      <div fl gap align-items=center>
        <p text mid>
          <i class="mi">stream</i>
        </p>
      </div>
      <p text bold midler>$followers_text</p>
      <p text smol>$followers_text_slogan</p>
    </div>
  </div>
</box-model>
TEXT;

$no_followers_text_slogan = __("There are no followers! Show some respect and follow this player.");
$follow_texterino = __("follow.short");
$no_followers = <<<TEXT
<box-model outlined mb=std>
  <div pblock48 pinline48>
    <div fl fldircol gap>
      <div fl gap align-items=center>
        <p text mid>
          <i class="mi">stream</i>
        </p>
      </div>
      <p text bold midler>$follow_texterino $User->name</p>
      <p text smol>$no_followers_text_slogan</p>
    </div>
  </div>
</box-model>
TEXT;

?>

<div fl fldircol gap=smol+>
  <?php if ($followers_count) { ?>
    <div title-inline>
      <p text bold mid><?= __("Followers") ?></p>
    </div>
  <?php } ?>

  <?php

  if (!$followers_count) {
    if ($is_my_profile)
      echo $no_followers_my;
    else
      echo $no_followers;
  } else {

  ?>

    <div fl fldircol gap=smol>
      <?php

      $followers_shown = 0;

      foreach ($Followers ?? [] as $key => $Follower) {
        if ($followers_shown == $show_followers_count)
          break;

        /**
         * Unset the follower from list.
         */
        unset($Followers[$key]);

        /**
         * Privacy settings
         */
        $follower_privacy = $Follower->privacy;

        /**
         * Hide profile is turned on, continue this user.
         */
        if (!$follower_privacy->is_public) continue;

        $followers_shown++;

        /**
         * If the current user is following this user already, show
         * a different button than the follow button.
         */
        $is_following = LOGGED && CurrentUser->follows($Follower);

        /**
         * Follower is current user?
         */
        $is_me = $Follower->id === CurrentUser->id;

        /**
         * The id for the image to be loaded and displayed properly.
         */
        $user_image_id = $Follower->id;

      ?>

        <box-model user-card filled=lighter <?php if ($Follower->is_restricted()) echo "disabled"; ?> clickable>
          <bm-inr size=smol fl alic gap=smol+ jucsb>
            <a link rounded=wide href="<?= $Follower->link(); ?>" ripple-effect></a>

            <div fl alic gap=smol+>
              <div class="image">
                <picture size=std circled>
                  <?php $Follower->image(); ?>
                </picture>
              </div>
              <div class="texter" flex-truncate>
                <div class="name">
                  <p text bold std color><?= $Follower->name(); ?></p>
                </div>
                <div class="last_activity">
                  <?php if (!$Follower->is_restricted()) { ?>
                    <p text smol trimt post-subcontent><?= __("Active") ?>
                      <?= Time::ago(date('Y-m-d h:i:s', $Follower->latest_activity), true); ?></p>
                  <?php } else { ?>
                    <div fl alic gap=smolest color=company>
                      <mi size=smol>raven</mi>
                      <p text smol><?= __("Restricted") ?></p>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>

            <?php

            /**
             * Include relationship actions.
             */
            include TEMPLATE . "/relationships/_actions.php"; ?>
          </bm-inr>
        </box-model>

      <?php } ?>

      <?php if (count($Followers) > 0) { ?>

        <div class="object_circle_row" mt=smol>
          <div fl alic jucc>
            <p text mid style=padding-left:.4em;margin-top:-.4em;>
              <i class="mi">subdirectory_arrow_right</i>
            </p>
          </div>

          <?php

          /**
           * Show more followers as circles next to each other on the bottom
           * of the followers section.
           */
          foreach ($Followers ?? [] as $key => $Follower) {
            /**
             * @var User $Follower
             */

            if ($followers_shown > ($show_followers_count + $show_followers_circle_count))
              break;

            /**
             * Unset the follower from list.
             */
            unset($Followers[$key]);

            /**
             * Privacy settings
             */
            $follower_privacy = $Follower->privacy;

            /**
             * Hide profile is turned on, continue this user.
             */
            if (!$follower_privacy->is_public) continue;

            /**
             * Increase the followers shown
             */
            $followers_shown++;

          ?>
            <div class=object_in_row has-tooltip=bottom>
              <a href="<?= "/u/" . $Follower->id; ?>" fl align-items=center gap style=flex:1;
                <?php if ($Follower->is_restricted()) echo "disabled"; ?>>

                <div class="object_in_row__outer" size=std>
                  <picture size=std circled>
                    <?php $Follower->image() ?>
                  </picture>
                </div>

                <div ttooltip>
                  <p text std>
                    <?php if ($Follower->is_restricted())  echo "<mi size=smol>raven</mi> &nbsp; "; ?>
                    <strong><?= $Follower->name; ?></strong>
                  </p>
                </div>
              </a>
            </div>
          <?php

          }

          /**
           * If more than the circle count has to offer?
           */
          $even_more = $followers_shown >= ($show_followers_count + $show_followers_circle_count);

          if ($even_more) {

          ?>
            <div class=has_more has-tooltip=bottom background=company color=invert fl alic jucc>
              <p text>+<?= $followers_count - $followers_shown; ?></p>
            </div>
          <?php } ?>
        </div>
      <?php } ?>

      <?php

      if (!$followers_shown && $Followers) {
        if ($is_my_profile)
          echo $no_followers_my;
        else
          echo $no_followers;
      }

      ?>
    </div>

  <?php } ?>

</div>