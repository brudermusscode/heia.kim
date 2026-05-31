<?php

use Heiakim\Model\User;

/**
 * @var User $User
 * @var int $gumode
 */

// TODO: Make these files only accessible through using the whole website.

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility) { ?>
  <get-content from="/user/get-content/most-played-artists?id=<?= $User->id; ?>&gumode=<?= $gumode; ?>&limit=6">
    <?php include COMPONENT . "/animations/_loading_content_artists.html"; ?>
  </get-content>
<?php } ?>