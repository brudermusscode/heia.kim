<?php

use Bruder\Heiakim\Model\Squad\SquadPost;

/**
 * @var SquadPost $Post
 */

?>

<div outlined=darker rounded=mid p24 fl alic jucc>
  <div fl alic gap="smol">
    <p text bold>Squad Post</p>
    <p text bold slighter mid style="margin-top:-.2em;">&middot;</p>
    <p text slight><?= ucfirst($Post->type); ?></p>
    <p text bold slighter mid style="margin-top:-.2em;">&middot;</p>
    <p text slight>ID: <?= $Post->id; ?></p>
  </div>
</div>