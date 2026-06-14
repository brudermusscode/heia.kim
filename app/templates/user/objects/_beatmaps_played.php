<?php

use Heiakim\Model\User;

/**
 * @var User $User
 * @var int $gumode
 * @var bool $has_played
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility) { ?>
  <div fl fldircol gap=smol+>
    <div fl alistart gap=smol+ title-inline>
      <p text bold ttup>Most played beatmaps</p>
    </div>
    <get-content from="/user/get-content/most-played-beatmaps?id=<?= $User->id; ?>&gumode=<?= $gumode; ?>">
      <?php include COMPONENT . "/animations/_loading_content_beatmaps.html"; ?>
    </get-content>
  </div>
<?php } ?>