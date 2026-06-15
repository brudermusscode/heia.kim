<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Application\Server;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Http\Request;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$type            = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);
$sub_type        = filter_input(INPUT_GET, "sub_type", FILTER_SANITIZE_SPECIAL_CHARS);
$attachment_id   = filter_input(INPUT_GET, "attachment_id", FILTER_VALIDATE_INT);
$attachment_type = filter_input(INPUT_GET, "attachment_type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var array
 */
$types = [
  "squad:post",
];

/**
 * @var array
 */
$attachment_types = [
  "beatmap:set",
  "score",
];

/**
 * Validate all types.
 */
if (!in_array($type, $types) || $attachment_id && !in_array($attachment_type, $attachment_types))
  exit($Request->error());

/**
 * User verified?
 */
authorize(resource: CurrentUser, respect_social_exclusion: true);

/**
 * ? squad:post
 */
if ($type === "squad:post") {

  if (!$sub_type)
    exit($Request->error());

  /**
   * @var Squad
   */
  $Squad = CurrentUser->squad;
} else
  exit($Request->error());

/**
 * @var ?Beatmap\Set
 */
$BeatmapSet = $attachment_id && $attachment_type === "beatmap:set"
  ? Beatmap\Set::find($attachment_id)
  : null;

/**
 * @var ?Score
 */
$Score = $attachment_id && $attachment_type === "score"
  ? Score::find($attachment_id)
  : null;

ob_start(); ?>

<?php include SNOW; ?>

<div posting-machine-overlay active rounded=wider animation=fade-in>
  <pm-inr post-type="<?= $sub_type; ?>" filled=lighter rounded=wider animation=open style=height:auto;>
    <form data-form="squad:post:create" responder=always>
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

          <!--- POST: TEXT --->
          <div post-type-input=text>
            <textarea autofocus filled=none w100 auto-resize material name=comment_string[text] placeholder="What's on your mind?"></textarea>
          </div>

          <!--- POST: POLL --->
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


          <?php

          /**
           * ? Score
           */
          if ($Score) :
            $clean_appearance = true;

            include TEMPLATE . "/score/_score.php";
          endif; ?>

          <?php

          /**
           * ? Beatmap\Set
           */
          if ($BeatmapSet) :

            /**
             * @var Beatmap
             */
            $Beatmap = $BeatmapSet->beatmaps()
              ->first();

            /**
             * @var int
             */
            $beatmaps_count = $BeatmapSet->beatmaps->count();

          ?>
            <div fl fldircol gap=smol>
              <p text bold smol ttup slight>Attachment</p>

              <div fl fldircol gap=smol>
                <a href="/beatmap-set/<?= $BeatmapSet->id ?>/<?= $Beatmap->id ?>">
                  <div class=notif__score clickable rounded>
                    <div class="cover">
                      <picture>
                        <?php $BeatmapSet->cover(); ?>
                      </picture>
                    </div>
                    <div class="notif__score_inr tac" p18>
                      <p text bold mid trimt><?= $Beatmap->stripped_title(); ?></p>
                      <p text trimt><?= $BeatmapSet->artists->first()->name; ?></p>

                      <div fl jucend>
                        <div pinline12 pblock6 background=company rounded fl alic gap=smol>
                          <mi pt4>page_control</mi>
                          <p text bold><?= $beatmaps_count ?> difficult<?= $beatmaps_count > 1 ? "ies" : "y" ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          <?php endif; ?>




          <div fl aliend jucsb w100>
            <mbutton tag filled=darker has-icon=left has-tooltip=bottom>
              <mi>vpn_lock</mi>
              <p text><?= $Squad->name ?></p>
              <div ttooltip>
                <p text bold>Only visible to squad members</p>
              </div>
            </mbutton>
            <mbutton mid background=company color=company-text icon-only submit-closest>
              <mi>prompt_suggestion</mi>
            </mbutton>
          </div>
        </div>
      </pm-content>
    </form>
  </pm-inr>
</div>

<?php

die($Request->success(data: ob_get_clean()));
