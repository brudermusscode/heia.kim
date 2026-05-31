<?php

use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Time\Time;

/**
 * @var array $Group
 * @var SquadPostComment $Comment
 */

/**
 * @var User
 */
$CommentUser = $Group[0]->user;

/**
 * @var bool
 */
$is_new ??= false;

/**
 * @var object
 */
$HighestRole = $CommentUser->squad_user->highest_privileges();

?>

<comment fl fldircol gap=smol>
  <div fl gap=smol+>
    <div fl alic gap=wide flexone>
      <t-o-c-icon circled>
        <a href="<?= $CommentUser->link(); ?>">
          <picture size=std circled clickable-zoom clickable>
            <?php $CommentUser->image(); ?>
          </picture>
        </a>
      </t-o-c-icon>

      <div fl alic flexone style=margin-left:.8em;>
        <div fl alic gap=smol>
          <a href="<?= $CommentUser->link(); ?>">
            <p text bold><?= $CommentUser->name(); ?></p>
          </a>
          &middot;
          <p text color=company><?= Time::ago($Group[0]->created_at); ?></p>
          &middot;
          <?php if ($CommentUser->squad_user->has_elevated_privileges()) { ?>
            <a href="<?= "$base_url/members"; ?>">
              <mbutton material size=smol background=invert color=invert has-icon=left>
                <mi><?= $HighestRole->icon; ?></mi>
                <p text bold><?= $HighestRole->name; ?></p>
              </mbutton>
            </a>
          <?php } ?>
        </div>

      </div>
    </div>
  </div>

  <div fl fldircol gap=smol>
    <?php foreach ($Group as $Comment) { ?>
      <div rounded p12 background=slighter rounded=mid>
        <p text><?= $Comment->comment_string; ?></p>
      </div>
    <?php } ?>
  </div>
</comment>