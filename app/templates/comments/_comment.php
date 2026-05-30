<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Comment;

/**
 * @var User $CurrentUser
 * @var Comment $Comment
 */

/**
 * @var User
 */
$User = $Comment->user;

/**
 * @var bool
 */
$is_new ??= false;

?>

<box-model comment data-id=<?= $Comment->id; ?> filled style=min-height:3.2em; <?php if ($is_new) echo "new-object"; ?>>

  <div object-actions menu-outer>
    <mbutton open-more-menu icon-only material size=smol filled>
      <mi>more_vert</mi>
    </mbutton>

    <jump-menu menu-more filled=lighter elevated color=dynamic>
      <?php if ($Comment->is_deletable_by($CurrentUser)) { ?>
        <form data-form="comments:remove">
          <div submit-closest class=jm__option hoverable>
            <mi>delete</mi>
            <p text std>Delete</p>
          </div>
        </form>
      <?php } ?>
    </jump-menu>
  </div>

  <bm-inr size=smol>
    <div fl gap=smol+ alistart>
      <picture size=smol circled>
        <?php $User->image(); ?>
      </picture>
      <div fl fldircol gap=smoler>
        <div fl gap=smol alic>
          <a href="<?= $User->link(); ?>" <?= $User->deleted ? "slighter" : ""; ?>>
            <p text std <?= $User->deleted ? "" : "bold"; ?>><?= $Comment->user->name; ?></p>
          </a>
          <p text std slight>&middot;</p>
          <p text std slight><?= Time::ago($Comment->created_at); ?></p>
        </div>

        <p text std><?= $Comment->comment_string; ?></p>
      </div>
    </div>
  </bm-inr>
</box-model>