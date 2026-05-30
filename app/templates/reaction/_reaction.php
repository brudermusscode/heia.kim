<?php

use Bruder\Heiakim\Model\Reaction;
use Bruder\Heiakim\Model\User;

/**
 * @var User $CurrentUser
 * @var Reaction $Reaction
 */

/**
 * @var Reaction
 */
$CurrentReactions = Reaction::where("reaction", $Reaction->reaction)
  ->where("type", $Reaction->type)
  ->where("reference_id", $Reaction->reference_id)
  ->get();

/**
 * @var int
 */
$have_reacted = $CurrentReactions->where("user_id", $CurrentUser->id)->count();

?>

<div class=reactions_option emoji data-reaction=<?= $Reaction->reaction; ?>
  <?php if ($have_reacted) echo "active"; ?>>
  <p count text bold smol><?= $CurrentReactions->count(); ?></p>
  <p text std style=margin-top:-.1em;><?= $Reaction->emoji->emoji; ?></p>
</div>