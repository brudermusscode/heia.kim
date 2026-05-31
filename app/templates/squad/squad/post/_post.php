<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;
use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostAttachment;

/**
 * @var User $User
 * @var SquadFeedItem $Item
 */

/**
 * @var User
 */
$User ??= $Post->user;

/**
 * @var SquadPost
 */
$Post ??= $Item->post;

/**
 * @var SquadFeedItem
 */
$Item ??= $Post->feed_item;

/**
 * @var Squad
 */
$Squad ??= $Post->squad;

/**
 * @var int
 */
$is_new ??= false;

/**
 * @var ?SquadPostAttachment
 */
$Attachment = $Post?->attachment?->reference();

?>

<t-object fl fldircol gap=smoler post data-id=<?= $Post?->id ?? 0; ?>>
  <t-o-toolbar background=bg>
    <div fl alic gap=smol+>
      <t-o-icon>
        <a href="<?= $User->link(); ?>">
          <picture size=std circled clickable-zoom clickable>
            <?php $User->image(); ?>
          </picture>
        </a>
      </t-o-icon>

      <t-o-creator fl alic gap=smol>
        <a href="<?= $User->link(); ?>">
          <p text bold><?= $User->name(); ?></p>
        </a>
        <p text bold slight>&middot;</p>
        <p text color=company><?= Time::ago($Post?->created_at ?? $Item->created_at, true); ?></p>
      </t-o-creator>
    </div>

    <?php

    /**
     * Show the dropdown only for squad members.
     */
    if (CurrentUser->sqcan_take_action_in($Squad))
      include __DIR__ . "/_dropdown.php"; ?>
  </t-o-toolbar>

  <?php

  /**
   * @var int
   */
  $comment_string_length = strlen($Post->comment_string ?? "");

  /**
   * @var array
   */
  $system_sub_type = explode("/", $Item->type);

  /**
   * Constructed file path based on the type of the post with
   * respect to system posts.
   *
   * @var string
   */
  $file_path = $Item->is_system_post()
    ? (
      isset($system_sub_type[2])
      ? dirname(__DIR__) . "/post/system/$system_sub_type[1]/_$system_sub_type[2].php"
      : dirname(__DIR__) . "/post/system/_$system_sub_type[1].php"
    )
    : dirname(__DIR__) . "/post/_" . $Post?->type . ".php";

  ?>

  <box-model id="squad-post-<?= ($Post ?? $Item)->id ?>" filled=lighter rounded=wide <?= $is_new ? "new-object" : ""; ?>>
    <div pinline28 pblock26 fl fldircol gap=smol+>

      <?php

      /**
       * Include the corresponding partial.
       */
      include file_exists($file_path) ? $file_path : dirname(__DIR__) . "/post/_unavailable.php"; ?>

      <?php

      if ($Attachment) : ?>
        <div fl fldircol gap=smol>

          <?php

          /**
           * ? Score
           */
          if ($Attachment instanceof Score) :

            /**
             * @var Score
             */
            $Score = $Attachment;
            $is_post = true;

          ?>
            <div fl alic gap=smol>
              <mi pt2 color=company>attachment</mi>
              <p text color=company>Score</p>
              <p text bold mid style=margin-top:-.2em;>&middot;</p>
              <a href="<?= $Score->user->link() ?>">
                <div fl alic gap=smol hoverable pinline4 pblock4 pr12 rounded>
                  <picture size=smol circled>
                    <?= $Score->user->image() ?>
                  </picture>
                  <p text><?= $Score->user->name() ?></p>
                </div>
              </a>
            </div>

          <?php include TEMPLATE . "/score/_score.php";
          endif;

          /**
           * ? Beatmap\Set
           */
          if ($Attachment instanceof Beatmap\Set) :

            /**
             * @var Beatmap\Set
             */
            $Set = $Attachment;
            $include_all_diffs = true;

          ?>
            <div fl alic gap=smol color=company>
              <mi pt2>attachment</mi>
              <p text>Beatmap</p>
            </div>

          <?php include TEMPLATE . "/beatmapset/_set-card.php";
          endif; ?>

        </div>
      <?php endif; ?>

      <?php

      if (
        CurrentUser->sqcan_take_action_in($Squad)
        && !$Item->is_system_post()
      ) {

        /**
         * @var ?SquadPostVote
         */
        $Vote = CurrentUser->has_voted_for($Post);

        /**
         * @var object
         */
        $feedback = (object) json_decode($Post->feedback, true);

      ?>

        <div fl aliend gap=smol jucsb mt=smol>

          <?php if ($Post->enable_comments) { ?>
            <div fl alic gap>
              <div fl gap=smol alic has-tooltip=bottom>
                <mbutton request-get="poster" request-get-attribute-type="squad_post_comment" request-get-attribute-id="<?= $Post->id; ?>" material filled size=std icon-only>
                  <mi>comment</mi>
                </mbutton>
                <p text bold> &middot; &nbsp;<span color=company comments-count><?= number_format($feedback->comments); ?></span></p>
                <div ttooltip>
                  <p text bold>Comments</p>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <mbutton disabled material tag size=std filled icon-only has-tooltip=bottom>
              <mi>comments_disabled</mi>
              <div ttooltip>
                <p text bold>Comments are disabled</p>
              </div>
            </mbutton>
          <?php } ?>

          <div fl alic votes>
            <div vote-count-button fl alic gap
              data-type="1"
              <?= ($Vote && $Vote->type === 1)
                ? "active data-action=squad:post:vote:delete"
                : "data-action=squad:post:vote:create"; ?>>
              <mbutton material hoverable size=std icon-only>
                <mi size=mid>keyboard_arrow_up</mi>
              </mbutton>
            </div>

            <?php

            /**
             * @var int
             */
            $vote_count = $Post->vote_count();

            ?>

            <div tac style=min-width:3.2em;>
              <p vote-count text bold <?= $vote_count > 0 ? "color=green" : ($vote_count < 0 ? "color=red" : "slighter"); ?>><?= $vote_count; ?></p>
            </div>

            <div vote-count-button fl alic gap=smol
              data-type="-1"
              <?= ($Vote && $Vote->type === -1)
                ? "active data-action=squad:post:vote:delete"
                : "data-action=squad:post:vote:create"; ?>>
              <mbutton clean material hoverable size=std icon-only>
                <mi size=mid>keyboard_arrow_down</mi>
              </mbutton>
            </div>
          </div>
        </div>

      <?php

      } else if ($Item->is_system_post()) { // end if item is system post

      ?>
        <div fl jucend mt=smolest alic gap=smol slighter>
          <mi size=smol>nights_stay</mi>
          <p text smol>Voting for system posts soon</p>
        </div>
      <?php } else { ?>

      <?php } ?>
    </div>
  </box-model>

  <?php

  if (
    !$Item->is_system_post()
    && $Post->enable_comments
  ) {

  ?>
    <div comments fl fldircol gap=smoler hide-empty>
      <?php

      /**
       * @var SquadPostComment
       */
      $Comments = $Post->comments;

      /**
       * @var int
       */
      $comments_start_count = 2;

      if ($Comments->count()) { ?>

        <div c-inner fl fldircol gap=smoler>

          <?php

          /**
           * @var SquadPostComment[]
           */
          // $GroupedComments = $Comments->group_by_users();

          foreach ($Comments->sortByDesc("created_at")->take($comments_start_count) as $Comment) {
            include dirname(__DIR__) . "/post/_comment.php";
          }

          ?>

        </div>

        <?php if ($Comments->count() > $comments_start_count) { ?>

          <div fl jucc>
            <mbutton fetch-more request-get-old="squads/post/comment/fetch" request-append-to="this.closest('[comments]').find('[c-inner]')" request-get-attribute-id="<?= $Post->id; ?>" request-get-attribute-limit=10 request-get-attribute-offset=<?= $comments_start_count; ?> size=std hoverable material outlined has-icon=left>
              <mi></mi>
              <p text bold></p>
            </mbutton>
          </div>

      <?php

        } // end if comments count > comments start count
      } // end if comments count

      ?>

      <t-o-icon>
        <mi>south_east</mi>
      </t-o-icon>

      <div c-line background=slighter></div>
    </div>
  <?php } // end if Post->comments_enabled
  ?>

</t-object>

<?php

/**
 * Clean up all variables.
 */
unset($Post, $User, $Comments, $is_new);
