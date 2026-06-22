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
  <div fl jucsb alic>
    <?php include TEMPLATE . "/squad/_tabs.php"; ?>
    <?php include TEMPLATE . "/squad/_placement.php"; ?>
  </div>

  <div column-wrapper>

    <?php if ($Squad->is_private() && !CurrentUser->sqcan_take_action_in($Squad)) { ?>

    <?php } else { ?>
      <div column=small fl fldircol gap=mid hide-mobile>
        <div outlined w100 style=height:100px; rounded></div>
      </div>

      <div column=large fl fldircol gap=wide flexone flex-truncate w100>
        <timeline posts>
          <t-line></t-line>

          <?php

          /**
           * @var SquadFeedItem
           */
          $FeedItems = $Squad->feed_items->sortByDesc("created_at");

          /**
           * @var ?SquadFeedItem
           */
          $LastItem = null;

          if (!$FeedItems->count()) : ?>
            <box-model rounded=wide filled=lighter p62 fl fldircol alic gap>
              <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
                <i class="mi" size=wide>whatshot</i>
              </div>
              <div tac>
                <p text bold wide><?= __("Nothing") ?></p>
                <p text std><?= __("No scores have been set here so far") ?></p>
              </div>
              <?php if ($Squad->is_member(CurrentUser)) { ?>
                <div fl justify-content=center>
                  <a href="/beatmaps">
                    <mbutton mid has-icon=left background=dynamic has-icon>
                      <mi>explore</mi>
                      <p text std bold><?= __("Explore beatmaps") ?></p>
                    </mbutton>
                  </a>
                </div>
              <?php } ?>
            </box-model>
          <?php endif;

          foreach ($FeedItems as $Item) :

            /**
             * @var User
             */
            $User = $Item->user;

            if (in_array($Item->type, SquadFeedItem::$types)) :

              # Should not happen, but if there is no post attached on a non-system-
              # post, continue to prevent any errors.
              if (!$Item->is_system_post() && !$Item->post)
                continue;

              include TEMPLATE . "/squad/post/_post.php";

              $LastItem = $Item;

              unset($Post, $User);
            endif;
          endforeach; ?>

        </timeline>
      </div>

      <div column=small fl fldircol gap=mid hide-tablet>
        <div outlined w100 style=height:100px; rounded></div>
      </div>
    <?php } ?>

  </div>
</div>