<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\Squad\SquadPost;

/**
 * @var Squad $Squad
 * @var User $CurrentUser
 * @var User $User
 * @var SquadPost $Post
 */

?>

<div fl fldircol gap=smol>
  <div fl alic gap=smol>
    <mi slight>format_quote</mi>
    <p text bold slighter mid style=margin-top:-.2em;>&middot;</p>
    <p text slight>Text</p>
  </div>
  <p <?= $comment_string_length < 30 ? "wide style=line-height:1.1;" : ($comment_string_length < 70 ? "mid style=line-height:1.2;" : "midler") ?>
    text><?= htmlspecialchars_decode($Post->comment_string); ?></p>
</div>