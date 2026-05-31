<?php

use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var SquadPost $Post
 * @var SquadFeedItem $Item
 */

?>

<div posrel menu-outer>
  <mbutton material clean size=std icon-only ripple-effect open-more-menu>
    <mi>page_info</mi>
  </mbutton>

  <jump-menu menu-more filled=lighter elevated color=dynamic>

    <?php if ($Item->is_system_post()) { ?>
      <form request="squad:feed-item:delete" responder reload>
        <input type=hidden name=id value=<?= $Item->id; ?> />
        <div submit-closest class=jm__option hoverable>
          <mi>delete</mi>
          <p text std>Delete</p>
        </div>
      </form>
    <?php } else { ?>

      <?php if (CurrentUser->sqcan_touch($Post ?? $Item)) { ?>
        <div class=jm__option hoverable disabled>
          <mi>edit</mi>
          <p text std>Edit</p>
        </div>

        <form request="squad:post:update" responder=error reload>
          <input type=hidden name=id value=<?= $Post->id; ?> />
          <input type=hidden name=enable_comments value=<?= $Post->enable_comments ? "0" : "1"; ?> />
          <div submit-closest class=jm__option hoverable>
            <mi><?= $Post->enable_comments ? "comments_disabled" : "comment"; ?></mi>
            <p text std><?= $Post->enable_comments ? "Disable" : "Enable"; ?> comments</p>
          </div>
        </form>

        <div divide=line></div>

        <form request="squad:post:delete" responder=error reload>
          <input type=hidden name=id value=<?= $Post->id; ?> />
          <div submit-closest class=jm__option hoverable>
            <mi>delete</mi>
            <p text std>Delete</p>
          </div>
        </form>
      <?php } else { ?>
        <div class=jm__option hoverable
          request-get="report:new"
          data-id=<?= $Post->id; ?>
          data-type=squad:post>
          <mi>campaign</mi>
          <p text std>Report to Content Guradian</p>
        </div>
      <?php } ?>
    <?php } ?>
  </jump-menu>
</div>