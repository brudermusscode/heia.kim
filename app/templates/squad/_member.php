<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;

/**
 * @var User
 */
$User = $Member->user;

/**
 * Whether this is the current user.
 */
$is_me = CurrentUser->id === $User->id;

/**
 * If the current user is following this user already, show
 * a different button than the follow button.
 */
$is_following = LOGGED && CurrentUser->follows($User);

?>

<box-model user-card filled=lighter <?php if ($User->is_restricted()) echo "disabled"; ?> clickable>
  <bm-inr size=smol fl alic gap=smol jucsb>
    <a rounded=wide link ripple-effect href="<?= $User->link(); ?>"></a>

    <div fl gap=smol+ alic>
      <div class="image">
        <picture size=std circled>
          <?php $User->image(); ?>
        </picture>
      </div>
      <div class="texter" flex-truncate>
        <div class="name">
          <p text bold std color><?= $User->name(); ?></p>
        </div>
        <div class="last_activity">
          <?php if (!$User->is_restricted()) { ?>
            <p text smol trimt post-subcontent>
              <?= __("Active") ?> <?= Time::ago(date('Y-m-d h:i:s', $User->latest_activity)); ?>
              <?= __("ago") ?>
            </p>
          <?php } else { ?>
            <div fl alic gap=smoler post-subcontent>
              <i class=mi size=smol>raven</i>
              <p text smol ttup><?= __("Restricted") ?></p>
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