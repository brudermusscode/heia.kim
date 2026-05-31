<?php

require_once _root() . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Comment;
use Heiakim\Model\Score;
use Heiakim\Model\User;

/**
 * @var Request $Request
 */

/**
 * User is logged in?
 * ! Error
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * Begin the output buffer.
 */
ob_start();

/**
 * @var ?Score
 */
$Scores = CurrentUser->scores;

/**
 * @var ?User
 */
$Squad = CurrentUser->squad;

/**
 * @var int
 */
$community_activity_count = CurrentUser->threads->count() + CurrentUser->thread_posts->count();

/**
 * @var ?Comment
 */
$comments_count = CurrentUser->comments->count();

/**
 * @var ?User
 */
$Followers = CurrentUser->followers;

/**
 * @var ?User
 */
$Followings = CurrentUser->followings;

/**
 * @var bool
 */
$has_any_data = $Scores->count() || $Squad || $community_activity_count || $comments_count || $Followers->count() || $Followings->count();

?>

<?php if (!$has_any_data) { ?>

  <box-model filled=lighter animation=fade-in>
    <bm-inr fl gap alic>
      <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
        <mi midler>face_retouching_natural</mi>
      </div>
      <div>
        <p text bold>How clean it is!</p>
        <p text>You have nothing to leave behind.</p>
      </div>
    </bm-inr>
  </box-model>

  <?php

} else {

  if ($Scores->count()) { ?>

    <box-model filled=lighter animation=fade-in>
      <bm-inr fl gap alic>
        <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
          <mi midler>trending_up</mi>
        </div>
        <p text><strong><?= $Scores->count(); ?> scores</strong> you have set</p>
      </bm-inr>
    </box-model>

  <?php

  }

  if ($Squad) { ?>

    <a href="<?= $Squad->link(); ?>">
      <box-model filled=lighter clickable animation=fade-in>
        <bm-inr fl alic jucsb>
          <div fl gap alic>
            <picture circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
              <?php $Squad->logo(); ?>
            </picture>
            <div fl fldircol gap=smoler>
              <div fl alic gap=smol>
                <div background="invert" rounded="wide" pinline10 pblock4 ttup>
                  <p text smol bold color="invert"><?= $Squad->tag; ?></p>
                </div>
                <p text><strong><?= $Squad->name; ?></strong></p>
              </div>
              <p text>Leaving your squad behind</p>
            </div>
          </div>
          <mi midler>east</mi>
        </bm-inr>
      </box-model>
    </a>

  <?php

  }

  if ($community_activity_count) { ?>

    <box-model filled=lighter animation=fade-in>
      <bm-inr fl alic gap>
        <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
          <mi midler>forum</mi>
        </div>
        <div fl fldircol gap=smoler>
          <p text><strong><?= $community_activity_count; ?> threads & posts</strong></p>
          <p text>Leaving our wonderful community</p>
        </div>
      </bm-inr>
    </box-model>

  <?php

  }

  if ($comments_count) { ?>

    <box-model filled=lighter slight>
      <bm-inr fl alic gap>
        <div circled outlined=darker style="height:3.2em;min-width:3.2em;" fl alic jucc>
          <mi midler>comments_disabled</mi>
        </div>
        <div fl fldircol gap=smoler>
          <p text><strong><?= $comments_count; ?> comments</strong> won't be deleted</p>
          <p text>For comments, we will remove everything that could be connected to your user identity. No image, no name,
            just the comment itself. Keeping it for the context only!</p>
        </div>
      </bm-inr>
    </box-model>

  <?php

  }

  if ($Followers->count()) { ?>

    <box-model filled=lighter animation=fade-in>
      <bm-inr fl gap alic>
        <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
          <mi midler>call_received</mi>
        </div>
        <div fl fldircol gap=smoler>
          <p text><strong><?= $Followers->count(); ?> players</strong> are following you</p>
          <p text>
            <?php foreach ($Followers->take(3) as $F) echo "<a normal href='/u/$F->id'>$F->name</a>, "; ?>...
          </p>
        </div>
      </bm-inr>
    </box-model>

  <?php

  }

  if ($Followings->count()) { ?>

    <box-model filled=lighter animation=fade-in>
      <bm-inr fl gap alic>
        <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
          <mi midler>call_made</mi>
        </div>
        <div fl fldircol gap=smoler>
          <p text><strong><?= $Followings->count(); ?> players</strong> are followed by you</p>
          <p text>
            <?php foreach ($Followings->take(3) as $F) echo "<a normal href='/u/$F->id'>$F->name</a>, "; ?>...
          </p>
        </div>
      </bm-inr>
    </box-model>

<?php

  }
}

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
