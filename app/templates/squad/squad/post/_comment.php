<?php

use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Time\Time;
use Heiakim\Model\User;

/**
 * @var SquadPost $Post
 * @var SquadPostComment $Comment
 */

/**
 * @var User
 */
$CommentUser = $Comment->user;

/**
 * @var bool
 */
$is_new ??= false;

?>

<box-model filled=lighter rounded=wide <?= $is_new ? "new-object" : ""; ?>>
  <bm-inr size=std>
    <div fl alistart gap=smol+>
      <a href="<?= $CommentUser->link(); ?>">
        <picture size=std circled clickable-zoom clickable>
          <?php $CommentUser->image(); ?>
        </picture>
      </a>
      <div flexone>
        <div fl alic jucsb w100>
          <div fl alic gap=smol>
            <a href="<?= $CommentUser->link(); ?>">
              <p text bold><?= $CommentUser->name(); ?></p>
            </a>
            &middot;
            <p text color=company><?= Time::ago($Comment->created_at); ?></p>
          </div>
          <div fl alic gap=smol>
            <?php

            if ($CommentUser->squad_user?->has_elevated_privileges()) {

              /**
               * @var object
               */
              $HighestRole = $CommentUser->squad_user->highest_privileges();

            ?>
              <a href="<?= "$base_url/members"; ?>">
                <mbutton material size=smol filled=darker has-icon=left>
                  <mi><?= $HighestRole->icon; ?></mi>
                  <p text bold><?= $HighestRole->name; ?></p>
                </mbutton>
              </a>
            <?php } ?>

            <div posrel menu-outer>
              <mbutton material clean size=smol icon-only ripple-effect open-more-menu>
                <mi>more_vert</mi>
              </mbutton>

              <jump-menu menu-more background=dynamic elevated color=dynamic>
                <?php if (CurrentUser->is($Comment->user) || CurrentUser->squad_user?->can_touch($Comment)) { ?>
                  <form request="squad:post:comment:delete" responder=error reload>
                    <input type=hidden name=id value=<?= $Comment->id; ?> />
                    <div submit-closest class=jm__option hoverable>
                      <mi>delete</mi>
                      <p text std>Delete</p>
                    </div>
                  </form>
                <?php } else if (CurrentUser->sqcan_take_action_in($Squad)) { ?>
                  <div class=jm__option hoverable
                    request-get="report:new"
                    data-id=<?= $Comment->id; ?>
                    data-type=squad:post:comment>
                    <mi>campaign</mi>
                    <p text std>Report to Content Guardian</p>
                  </div>
                <?php } else { ?>
                  <p pblock24 pinline10 text slighter>Nothing here</p>
                <?php } ?>
              </jump-menu>
            </div>
          </div>
        </div>

        <p text><?= $Comment->comment_string; ?></p>
      </div>
    </div>
  </bm-inr>
</box-model>