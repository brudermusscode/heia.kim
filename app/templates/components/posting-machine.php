<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Squad;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;

authorize(resource: CurrentUser, respect_social_exclusion: true);

$type            = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);
$sub_type        = filter_input(INPUT_GET, "sub_type", FILTER_SANITIZE_SPECIAL_CHARS);
$attachment_id   = filter_input(INPUT_GET, "attachment_id", FILTER_VALIDATE_INT);
$attachment_type = filter_input(INPUT_GET, "attachment_type", FILTER_SANITIZE_SPECIAL_CHARS);

$types = [
  "squad:post",
];

$attachment_types = [
  "beatmap:set",
  "score",
];

if (
  !in_array($type, $types)
  || $attachment_id && !in_array($attachment_type, $attachment_types)
)
  exit($Request->error());

if ($type === "squad:post") {
  if (!$sub_type)
    exit($Request->error());

  /**
   * @var Squad
   */
  $Squad = CurrentUser->squad;
} else
  die(error());

/**
 * @var ?Beatmap\Set
 */
$Set = $attachment_id && $attachment_type === "beatmap:set"
  ? Beatmap\Set::find($attachment_id)
  : null;

/**
 * @var ?Score
 */
$Score = $attachment_id && $attachment_type === "score"
  ? Score::find($attachment_id)
  : null;

$has_attachment = $Set || $Score;

ob_start();

include SNOW; ?>

<posting-machine active rounded=wider animation=fade-in>
  <form request="squad:post:create" responder=always close-overlay>
    <pm-inr post-type="<?= $sub_type; ?>" filled=lighter rounded=wider animation=open style=height:auto;>

      <input type=hidden name=type value=<?= $sub_type ?> />

      <?php if ($attachment_id && $attachment_type) : ?>
        <input type=hidden name=attachment_id value=<?= $attachment_id ?> />
        <input type=hidden name=attachment_type value=<?= $attachment_type ?> />
      <?php endif; ?>

      <pm-content active>
        <div post-types fl alic gap jucsb>
          <div fl alic gap>
            <picture size=std circled>
              <?php CurrentUser->image(); ?>
            </picture>
            <div flexone>
              <p title text>What's on your mind?</p>
              <p user text bold><?= CurrentUser->name(); ?></p>
            </div>
          </div>

          <div fl alic gap=smol>
            <mbutton create-post="text" icon-only filled=clean has-tooltip=bottom>
              <mi size=midler>format_quote</mi>
              <div ttooltip>
                <p text bold>Just text</p>
              </div>
            </mbutton>
            <mbutton create-post="poll" icon-only filled=clean has-tooltip=bottom>
              <mi size=midler>ballot</mi>
              <div ttooltip>
                <p text bold>Poll</p>
              </div>
            </mbutton>
          </div>
        </div>

        <div post-type-content>

          <!--- Text --->
          <div post-type-input=text>
            <textarea autofocus filled=none w100 auto-resize material name=comment_string[text] placeholder="What's on your mind?"></textarea>
          </div>

          <!--- Poll --->
          <div post-type-input=poll fl fldircol gap=smol>
            <textarea autofocus auto-resize name=comment_string[poll] placeholder="Should we go for #1?"></textarea>

            <div poll-options fl fldircol gap=smol>
              <div p-option fl alic gap=smol outlined=darker rounded=wider>
                <mi size=mid slight></mi>
                <input flexone type=text name=options[] placeholder="Yeah, let's go!" />
                <mbutton delete-option icon-only background=slighter hoverable z>
                  <mi size=midler></mi>
                </mbutton>
              </div>
              <div p-option fl alic gap=smol outlined=darker rounded=wider>
                <mi size=mid slight></mi>
                <input flexone type=text name=options[] placeholder="Nah m8." />
                <mbutton delete-option icon-only background=slighter hoverable z>
                  <mi size=midler></mi>
                </mbutton>
              </div>
            </div>

            <div w100 fl jucc>
              <mbutton add-option has-icon=left filled hoverable>
                <mi>add</mi>
                <p text bold>Add option</p>
              </mbutton>
            </div>
          </div>

          <!--- Attachment --->
          <?php if ($has_attachment) : ?>
            <div fl fldircol gap=smol>
              <p text smol bold ttup slight>Attachment</p>
              <?php

              # Include attachments.
              if ($Score) :
                $clean_appearance = true;

                include TEMPLATE . "/score/_score.php";
              endif;

              if (($Set)) :
                include TEMPLATE . "/beatmap/_beatmap-row.php";
              endif;

              # Add more.

              ?>
            </div>
          <?php endif; ?>

          <!--- Actions --->
          <div fl aliend jucsb w100>
            <mbutton tag filled=darker has-icon=left has-tooltip=bottom>
              <mi>vpn_lock</mi>
              <p text><?= $Squad->name ?></p>
              <div ttooltip>
                <p text bold>Only visible to squad members</p>
              </div>
            </mbutton>
            <mbutton mid background=invert color=invert icon-only submit-closest>
              <mi>send</mi>
            </mbutton>
          </div>
        </div>
      </pm-content>
    </pm-inr>
  </form>
</posting-machine>

<?php die(success(data: ob_get_clean()));
