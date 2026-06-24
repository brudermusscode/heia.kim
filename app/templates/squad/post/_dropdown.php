<?php

use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var SquadPost $Post
 * @var SquadFeedItem $Item
 */

?>

<div posrel menu-outer>
  <mbutton clean icon-only ripple-effect open-more-menu>
    <mi>page_info</mi>
  </mbutton>

  <jump-menu menu-more filled=lighter elevated color=dynamic>

    <?php

    # These are shown in case of a SquadFeedItem without a Post associated to it.
    if ($Item->is_system_post()) : ?>

      <form request="squad:feed-item:delete" shadow-submit delete-object="FeedItem" responder=simple>
        <input type=hidden name=id value=<?= $Item->id; ?> />
        <div submit-closest class=jm__option hoverable>
          <mi>delete</mi>
          <p text std>Delete</p>
        </div>
      </form>

    <?php else : ?>

      <?php if (CurrentUser->sqcan_touch($Post ?? $Item)) : ?>
        <div class=jm__option hoverable disabled>
          <mi>edit</mi>
          <p text std>Edit</p>
        </div>

        <div request="squad:post:update"
          data-id="<?= $Post->id; ?>"
          data-enable-comments="<?= $Post->enable_comments ? "0" : "1"; ?>"
          shadow-submit responder=error reload-object="Post"
          class=jm__option hoverable>
          <mi><?= $Post->enable_comments ? "comments_disabled" : "comment"; ?></mi>
          <p text std><?= $Post->enable_comments ? "Disable" : "Enable"; ?> comments</p>
        </div>

        <div divide=line></div>

        <div request="squad:post:delete" data-id="<?= $Post->id; ?>"
          shadow-submit delete-object="Post" responder=simple class=jm__option hoverable>
          <mi>delete</mi>
          <p text std>Delete</p>
        </div>
      <?php else : ?>
        <div class=jm__option hoverable
          request-get="report:new"
          data-id="<?= $Post->id; ?>"
          data-type="squad:post">
          <mi>campaign</mi>
          <p text std>Report to Content Guradian</p>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </jump-menu>
</div>