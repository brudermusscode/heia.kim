<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;
use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostAttachment;
use Heiakim\Model\Squad\SquadPostComment;
use Heiakim\Model\Squad\SquadPostVote;

/**
 * @var SquadFeedItem $Item
 * @var ?SquadPost $Post
 * @var ?Squad $Squad
 * @var User $User
 */

/**
 * @var SquadPost
 */
$Post ??= $Item->post;

/**
 * @var User
 */
$User ??= $Post->user;

/**
 * @var SquadFeedItem
 */
$Item ??= $Post->feed_item;

/**
 * @var Squad
 */
$Squad ??= $Post->squad;

/**
 * @var ?SquadPostAttachment
 */
$Attachment = $Post?->attachment?->reference();

$is_new ??= false;
$comment_string_length = strlen($Post->comment_string ?? "");
$system_sub_type = explode("/", $Item->type);

$simple = $Item->is_simple_post();

$file_path = $Item->is_system_post()
  ? (
    isset($system_sub_type[2])
    ? __DIR__ . "/type/system/$system_sub_type[1]/_$system_sub_type[2].php"
    : __DIR__ . "/type/system/_$system_sub_type[1].php"
  )
  : __DIR__ . "/type/_" . $Post?->type . ".php";

if (file_exists($file_path)) : ?>

  <t-object post data-id=<?= $Post?->id ?? 0; ?> fl fldircol gap=smoler>
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

          <?php if ($simple) : ?>
            <div fl alic gap=smol slight>
              <mi>waving_hand</mi>
              <p text bold>left</p>
              <p text bold>&middot;</p>
            </div>
          <?php endif; ?>

          <p text color=company><?= Time::ago($Post?->created_at ?? $Item->created_at, true); ?></p>
        </t-o-creator>
      </div>

      <?php if (CurrentUser->sqcan_take_action_in($Squad))
        include __DIR__ . "/_dropdown.php"; ?>
    </t-o-toolbar>

    <?php if (!$simple) : ?>
      <box-model filled=lighter rounded=wide
        id="squad-post-<?= ($Post ?? $Item)->id ?>"
        <?= $is_new ? "new-object" : ""; ?>>
        <div pinline28 pblock26 fl fldircol gap=smol+>
          <?php

          include file_exists($file_path)
            ? $file_path
            : __DIR__ . "/_unavailable.php"; ?>

          <!--- Attachment --->
          <?php if ($Attachment) : ?>
            <div fl fldircol gap=smol>

              <?php

              # + Attachment: Score
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

              # + Attachment: Beatmap\Set
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

              <?php include TEMPLATE . "/beatmap/_beatmap-row.php";
              endif; ?>
            </div>
          <?php endif; ?>

          <?php if (
            CurrentUser->sqcan_take_action_in($Squad)
            && !$Item->is_system_post()
          ) :

            /**
             * @var ?SquadPostVote
             */
            $Vote = CurrentUser->has_voted_for($Post);

            $feedback = (object) json_decode($Post->feedback, true);

          ?>

            <div fl aliend gap=smol jucsb mt=smol>

              <?php if ($Post->enable_comments) { ?>
                <div fl alic gap>
                  <div fl gap=smol alic has-tooltip=bottom>
                    <mbutton filled icon-only
                      request-get="poster"
                      request-get-attribute-type="squad_post_comment"
                      request-get-attribute-id="<?= $Post->id; ?>">
                      <mi>comment</mi>
                    </mbutton>
                    <p text bold> &middot; &nbsp;<span color=company comments-count><?= number_format($feedback->comments); ?></span></p>
                    <div ttooltip>
                      <p text bold>Comments</p>
                    </div>
                  </div>
                </div>
              <?php } else { ?>
                <mbutton disabled tag filled icon-only has-tooltip=bottom>
                  <mi>comments_disabled</mi>
                  <div ttooltip>
                    <p text bold>Comments are disabled</p>
                  </div>
                </mbutton>
              <?php } ?>

              <div fl alic votes>
                <div vote-count-button fl alic gap
                  shadow-submit reload-object="Post" responder=error
                  <?= ($Vote && $Vote->type === 1)
                    ? 'request="squad:post:vote:delete" active'
                    : 'request="squad:post:vote:create"' ?>
                  data-id="<?= $Post->id ?>"
                  data-type="1">
                  <mbutton hoverable icon-only>
                    <mi size=mid>keyboard_arrow_up</mi>
                  </mbutton>
                </div>

                <?php

                $vote_count = $Post->vote_count();

                ?>

                <div tac style=min-width:3.2em;>
                  <p vote-count text bold <?= $vote_count > 0 ? "color=green" : ($vote_count < 0 ? "color=red" : "slighter"); ?>><?= $vote_count; ?></p>
                </div>

                <div vote-count-button fl alic gap=smol
                  shadow-submit reload-object="Post" responder=error
                  <?= ($Vote && $Vote->type === -1)
                    ? 'request="squad:post:vote:delete" active '
                    : 'request="squad:post:vote:create"' ?>
                  data-id="<?= $Post->id ?>"
                  data-type="-1">
                  <mbutton clean hoverable icon-only>
                    <mi size=mid>keyboard_arrow_down</mi>
                  </mbutton>
                </div>
              </div>
            </div>

          <?php elseif ($Item->is_system_post()) : ?>
            <div fl jucend mt=smolest alic gap=smol slighter title-inline>
              <mi size=smol>nights_stay</mi>
              <p text smol>Voting not yet available</p>
            </div>
          <?php else : ?>
            Hello?
          <?php endif; ?>
        </div>
      </box-model>

      <?php if (!$Item->is_system_post() && $Post->enable_comments) : ?>
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
                <mbutton fetch-more hoverable outlined has-icon=left
                  request-get-old="squads/post/comment/fetch"
                  request-append-to="this.closest('[comments]').find('[c-inner]')"
                  request-get-attribute-id="<?= $Post->id; ?>"
                  request-get-attribute-limit=10
                  request-get-attribute-offset="<?= $comments_start_count; ?>">
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
      <?php endif; ?>
    <?php endif; ?>

  </t-object>

<?php endif;

# Clean up some variables.
// unset($Post, $User, $Comments, $is_new);
