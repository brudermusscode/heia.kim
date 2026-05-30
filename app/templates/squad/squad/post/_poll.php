<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\Squad\SquadPost;

/**
 * @var Squad $Squad
 * @var User $CurrentUser
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
 * @var int
 */
$is_new ??= false;

?>

<?php

/**
 * @var object

 */
$options = (object) json_decode($Post->comment_string, true);

/**
 * @var string
 */
$comment_string = $options->comment_string;

/**
 * Unset the comment string, so we are left with only the
 * poll options!
 */
unset($options->comment_string);

/**
 * Poll answers.
 */

/**
 * @var int
 */
$answers_given = 0;

/**
 * Add up the poll vote count.
 */
foreach ($options as $count)
  $answers_given += $count;

?>

<div fl fldircol gap=smol+>
  <div fl fldircol gap=smol>
    <div fl alic gap=smol>
      <mi slight>ballot</mi>
      <p text bold slighter mid style=margin-top:-.2em;>&middot;</p>
      <p text slight>Poll</p>
      <p text bold slighter mid style=margin-top:-.2em;>&middot;</p>
      <p text color=company><?= number_format($answers_given); ?> votes</p>
    </div>
    <p text pblock10 <?= $comment_string_length < 30 ? "wide style=line-height:1.1;" : ($comment_string_length < 70 ? "mid style=line-height:1.2;" : "midler") ?>><?= $comment_string; ?></p>
  </div>

  <div fl fldircol>
    <?php

    /**
     * @var SquadPostPollAnswer
     */
    $Answers = $Post->poll_answers;

    /**
     * @var int
     */
    $iota = 0;

    /**
     * @var ?SquadPostPollAnswer
     */
    $CurrentUserAnswer = $CurrentUser->has_answered_poll($Post);

    foreach ($options as $option => $count) {

      /**
       * @var bool
       */
      $has_answered_this = $CurrentUserAnswer !== null ? (int) $CurrentUserAnswer?->answer_key === $iota : null;

      /**
       * @var SquadPostPollAnswer
       */
      $CurrentAnswers = $Answers->filter(function ($answer) use ($iota) {
        return $answer->answer_key == $iota;
      })
        ->values();

      /**
       * @var int
       */
      $percent = $count ? number_format($count / $answers_given * 100, 1) : 0;

    ?>

      <div poll-answer p18 rounded=mid hoverable
        data-answer-key="<?= $has_answered_this ? $CurrentUserAnswer->id : $iota; ?>"
        <?= $has_answered_this ? 'active data-action="squad:post:poll-answer:delete" animation=fade-in' : 'data-action="squad:post:poll-answer:create"'; ?>>
        <div fl alic gap>
          <mi checked rounded=mid></mi>

          <div posrel fl fldircol gap=smol rounded=wide flexone w100>
            <div fl alic jucsb gap w100>
              <div fl alic jucsb flexone>
                <div>
                  <p text midler bold><?= $option; ?></p>
                  <?php if ($has_answered_this) { ?>
                    <p text smol>You voted &middot; <span color=company><?= Time::ago($CurrentUserAnswer->created_at, true); ?></span></p>
                  <?php } ?>
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
              <div posrel rounded=wide background=company rounded=wide style="height:24px;min-width:24px;width:calc(<?= $percent; ?>%);">
                <div style="position:absolute;right:0;top:50%;translate:0 -50%;height:.6em;width:.6em;margin-right:8px;border:3px solid white;" circled></div>
              </div>
              <div flexone background=slight rounded=wide style=height:12px;margin-left:.4em;></div>
            </div>
          </div>
        </div>
      </div>

    <?php

      $iota++;
    }

    ?>

  </div>
</div>

<?php

unset($iota);
