<?php

require_once ROOT . "/config/get_requirements.php";

use Heiakim\Model\Score;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Illuminate\Support\Collection;

authorize(CurrentUser);

/**
 * @var Collection<Score>
 */
$Scores = CurrentUser->scores;

/**
 * @var ?Squad
 */
$Squad = CurrentUser->squad;

/**
 * @var Collection<User>
 */
$Followers = CurrentUser->followers;

/**
 * @var Collection<User>
 */
$Followings = CurrentUser->followings;

$community_activity_count =
  CurrentUser->threads->count() + CurrentUser->thread_posts->count();
$comments_count = CurrentUser->comments->count();
$has_any_data = $Scores->count() || $Squad || $community_activity_count || $comments_count || $Followers->count() || $Followings->count();

ob_start();

if (!$has_any_data) : ?>

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

  <?php else :

  if ($Scores->count()) : ?>

    <div p24 rounded filled fl alic gap=smol+ animation=fade-in>
      <mi midler circled outlined=darker style="height:42px;width:42px;">
        trending_up</mi>
      <p text><strong color=company><?= $Scores->count(); ?> scores</strong>
        you have set</p>
    </div>

  <?php endif;

  if ($Squad) : ?>

    <a href="<?= $Squad->link(); ?>">
      <div p24 rounded filled fl alic jucsb clickable gap=smol+ animation=fade-in>
        <div fl gap alic>
          <picture circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
            <?php $Squad->logo(); ?>
          </picture>
          <div fl fldircol gap=smoler>
            <div fl alic gap=smol>
              <p text smol bold background="invert" rounded="wide" pinline6 pblock2 ttup color="invert"><?= $Squad->tag; ?></p>
              <p text><strong color=company><?= $Squad->name; ?></strong></p>
            </div>
            <p text>Leaving your squad behind</p>
          </div>
        </div>
        <mi midler>east</mi>
      </div>
    </a>

  <?php endif;

  if ($community_activity_count) : ?>

    <div p24 rounded filled fl alic gap=smol+ animation=fade-in>
      <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
        <mi midler>forum</mi>
      </div>
      <div fl fldircol gap=smoler>
        <p text><strong><?= $community_activity_count; ?> threads & posts</strong></p>
        <p text>Leaving our wonderful community</p>
      </div>
    </div>

  <?php endif;

  if ($comments_count) : ?>

    <div p24 rounded filled fl alic gap=smol+ animation=fade-in>
      <div circled outlined=darker style="height:3.2em;min-width:3.2em;" fl alic jucc>
        <mi midler>comments_disabled</mi>
      </div>
      <div fl fldircol gap=smoler>
        <p text><strong><?= $comments_count; ?> comments</strong> won't be deleted</p>
        <p text>For comments, we will remove everything that could be connected to your user identity. No image, no name,
          just the comment itself. Keeping it for the context only!</p>
      </div>
    </div>

  <?php endif;

  if ($Followers->count()) : ?>

    <div p24 rounded filled fl alic gap=smol+ animation=fade-in>
      <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
        <mi midler>call_received</mi>
      </div>
      <div fl fldircol gap=smoler>
        <p text><strong><?= $Followers->count(); ?> players</strong> are following you</p>
        <p text>
          <?php foreach ($Followers->take(3) as $F) echo "<a normal href='/u/$F->id'>$F->name</a>, "; ?>...
        </p>
      </div>
    </div>

  <?php endif;

  if ($Followings->count()) : ?>

    <div p24 rounded filled fl alic gap=smol+ animation=fade-in>
      <div circled outlined=darker style="height:3.2em;width:3.2em;" fl alic jucc>
        <mi midler>call_made</mi>
      </div>
      <div fl fldircol gap=smoler>
        <p text><strong><?= $Followings->count(); ?> players</strong> are followed by you</p>
        <p text>
          <?php foreach ($Followings->take(3) as $F) echo "<a normal href='/u/$F->id'>$F->name</a>, "; ?>...
        </p>
      </div>
    </div>

<?php endif;
endif;

die(success(data: ob_get_clean()));
