<?php

use Heiakim\Model\User;

/**
 * @var User $User
 * @var int $gumode
 * @var bool $has_played
 * @var int $fetch_limit
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility) { ?>
  <div fl fldircol gap=smol+>
    <div title-inline>
      <p text bold mid><?= __("Recent") ?></p>
    </div>
    <get-content from="/user/get-content/recent-scores?id=<?= $User->id; ?>&gumode=<?= $gumode; ?>">
      <?php include COMPONENT . "/animations/_loading_content_scores.html"; ?>
    </get-content>
  </div>
<?php } ?>