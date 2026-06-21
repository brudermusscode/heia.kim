<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadPost;

/**
 * @var Squad $Squad
 * @var User $User
 * @var SquadPost $Post
 */

?>

<div fl fldircol gap=smol>
  <div fl alic gap=smol slight>
    <mi>format_quote</mi>
    <p text bold mid style=margin-top:-6px;>&middot;</p>
    <p text>Text</p>
  </div>

  <p <?= $comment_string_length < 30 ? "wide style=line-height:1.1;" : ($comment_string_length < 70 ? "mid style=line-height:1.2;" : "midler") ?>
    text><?= htmlspecialchars_decode($Post->comment_string); ?></p>
</div>