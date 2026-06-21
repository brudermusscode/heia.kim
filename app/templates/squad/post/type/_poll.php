<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostPollAnswer;

/**
 * @var User $User
 * @var Squad $Squad
 * @var SquadFeedItem $Item
 */

/**
 * @var SquadPost
 */
$Post ??= $Item->post;

/**
 * @var User
 */
$User ??= $Post->user;

$is_new ??= false;
$options = (object) json_decode($Post->comment_string, true);
$comment_string = $options->comment_string;

# Only want the poll options.
unset($options->comment_string);

$answers_given = 0;

# Sum up the poll vote count.
foreach ($options as $count)
  $answers_given += $count;

?>

<div fl fldircol gap=smol+>
  <div fl fldircol gap=smol>
    <div fl alic gap=smol slight>
      <mi>ballot</mi>
      <p text bold mid style=margin-top:-6px;>&middot;</p>
      <p text>Poll</p>
      <p text bold mid style=margin-top:-6px;>&middot;</p>
      <p text color=company><?= number_format($answers_given); ?> votes</p>
    </div>

    <p text pblock10
      <?= $comment_string_length < 30
        ? "wide style=line-height:1.1;"
        : (
          $comment_string_length < 70
          ? "mid style=line-height:1.2;"
          : "midler"
        ) ?>>
      <?= $comment_string; ?></p>
  </div>

  <div fl fldircol>
    <?php

    /**
     * @var SquadPostPollAnswer
     */
    $Answers = $Post->poll_answers;

    $iota = 0;

    /**
     * @var ?SquadPostPollAnswer
     */
    $CurrentUserAnswer = CurrentUser->has_answered_poll($Post);

    foreach ($options as $option => $count) :

      /**
       * @var SquadPostPollAnswer
       */
      $CurrentAnswers = $Answers->filter(function ($answer) use ($iota) {
        return $answer->answer_key == $iota;
      })
        ->values();

      $has_answered_this = $CurrentUserAnswer !== null
        ? (int) $CurrentUserAnswer?->answer_key === $iota
        : null;

      $percent = $count ? number_format($count / $answers_given * 100, 1) : 0;

    ?>

      <div poll-answer pblock8 pl8 pr18 rounded=mid hoverable
        shadow-submit reload-object="Post" responder=error
        <?= $has_answered_this ?
          'request="squad:post:poll-answer:delete" animation=fade-in active'
          : 'request="squad:post:poll-answer:create"'; ?>
        data-answer-key="<?= $has_answered_this ? $CurrentUserAnswer->id : $iota; ?>"
        data-id="<?= $has_answered_this ? $CurrentUserAnswer->id : $Post->id ?>">
        <div fl alic gap>
          <mi checked rounded></mi>

          <div posrel fl fldircol gap=smol rounded=wide flexone w100>
            <div fl alic jucsb gap w100>
              <div fl alic jucsb flexone>
                <div>
                  <p text midler bold><?= $option; ?></p>
                  <?php if ($has_answered_this) : ?>
                    <p text smol>You voted &middot; <span color=company><?= Time::ago($CurrentUserAnswer->created_at, true); ?></span></p>
                  <?php endif; ?>
                </div>
                <div fl alic gap=smol>
                  <p text color=company><?= number_format($count); ?></p>
                  <p text bold slighter mid style=margin-top:-.2em;>&middot;</p>
                  <p text><?= number_format($percent, 1); ?> %</p>
                </div>
              </div>
            </div>

            <div fl alic>
              <!-- <div style="height:12px;min-width:32px;width:calc(<?= $percent; ?>%);background:url(/assets/images/curly-line.png) left center repeat-x;" rounded=wide posrel> -->
              <div posrel rounded=wide background=company rounded=wide style="height:18px;min-width:24px;width:calc(<?= $percent; ?>%);">
                <div style="position:absolute;right:0;top:50%;translate:0 -50%;height:.6em;width:.6em;margin-right:8px;border:3px solid white;" circled dno></div>
              </div>
              <?php if ($percent < 100) : ?>
                <div flexone background=slight rounded=wide
                  style=height:18px;margin-left:.4em;></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    <?php $iota++;
    endforeach;

    unset($iota); ?>

  </div>
</div>