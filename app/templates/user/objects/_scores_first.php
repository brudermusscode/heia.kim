<?php

use Bruder\Heiakim\Model\User;

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

if ($object_visibility && $has_played) { ?>
  <div fl fldircol gap=smol+>
    <div fl fldircol alistart gap=smoler title-inline>
      <p text bold mid><?= __("First places") ?></p>
      <div fl alic gap=smoler>
        <p text smol bold filled=darker pinline12 pblock6 rounded=wide>🏅 Ranked</p>
        <p text smol bold filled=darker pinline12 pblock6 rounded=wide>❤️ Loved</p>
      </div>
    </div>
    <get-content from="/user/get-content/first-placed-scores?id=<?= $User->id; ?>&gumode=<?= $gumode; ?>">
      <?php include COMPONENT . "/animations/_loading_content_scores.html"; ?>
    </get-content>
  </div>
<?php } ?>