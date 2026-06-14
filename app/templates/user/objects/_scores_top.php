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
    <div fl alic gap=smol+ title-inline>
      <p text bold ttup><?= __("Highest performances") ?></p>
      <p text smol bold filled=darker pinline12 pblock6 rounded=wide>🏅 Ranked</p>
    </div>
    <get-content from="/user/get-content/top-scores?id=<?= $User->id; ?>&gumode=<?= $gumode; ?>">
      <?php include COMPONENT . "/animations/_loading_content_scores.html"; ?>
    </get-content>
  </div>
<?php } ?>