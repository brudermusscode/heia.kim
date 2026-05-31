<?php

use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var Squad $Squad
 * @var string $base_url
 * @var string $mode
 * @var string $page
 */

?>

<div page-structure="squad">

  <?php

  /**
   * Include feed tabs.
   */
  include dirname(__DIR__) . "/_tabs.php"; ?>

  <div column-wrapper>

    <?php if ($Squad->is_private() && !CurrentUser->sqcan_take_action_in($Squad)) { ?>

    <?php } else { ?>
      <div column=small fl fldircol gap=mid hide-tablet>
      </div>

      <div column=large fl fldircol gap=wide flexone w100>
        <timeline posts>
          <t-line></t-line>

          <?php

          /**
           * @var SquadFeedItem
           */
          $FeedItems = $Squad
            ->feed_items
            ->sortByDesc("created_at");

          /**
           * @var ?SquadFeedItem
           */
          $LastItem = null;

          foreach ($FeedItems as $Item) :

            /**
             * @var User
             */
            $User = $Item->user;

            /**
             * ? POST
             */
            if (in_array($Item->type, ["__post__", "__squad__/created", "__squad__/edit/image+logo", "__squad__/edit/image+headline"])) :

              /**
               * Should not happen, but if there is no post
               * attached on a non-system-post, continue to
               * prevent any errors.
               */
              if (!$Item->is_system_post() && !$Item->post)
                continue;

              /**
               * Include the post partial. This will include the
               * correct type and all comments and other
               * belongings of it. This should also be included when
               * posting a new post.
               */
              include dirname(__DIR__) . "/post/_post.php";

              /**
               * Set the last Item to the current one.
               */
              $LastItem = $Item;
            endif;
          endforeach;

          ?>

        </timeline>
      </div>

      <div column=small hide-tablet>

      </div>
    <?php } ?>

  </div>
</div>