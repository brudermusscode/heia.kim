<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\Restriction\Restriction;
use Heiakim\Model\Restriction\RestrictionAppeal;
use Heiakim\Model\User;
use Heiakim\Time\Time;

/**
 * @var User $CurrentUser
 * @var Request $Request
 */

/**
 * User verified?
 * ! Error
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * This dialogue should only be viewable by the user, if they are
 * currently restricted (locked). Return an error, if not.
 * ! Error
 */
if (!RESTRICTED)
  exit($Request->error("!RESTRICTED"));

/**
 * @var Restriction
 */
$Restriction = $CurrentUser->restrictions()
  ->latest()
  ->first();

/**
 * @var ?RestrictionAppeal
 */
$Appeal = $CurrentUser->current_appeal_after_restriction_waiting_period();

/**
 * @var bool
 */
$is_editable = !$Appeal || !$Appeal->live_play_file;

/**
 * Begin output buffer.
 */
ob_start();

/**
 * Include snow animation. Can be toggled by the user.
 */
include SNOW;

?>

<form data-form="users:appeal,<?= $Appeal ? "update" : "create"; ?>">
  <div content-width=smoler prompt-height>

    <box-model prompt filled=lighter elevated>
      <div prompt-content>
        <div prompt-header>
          <mi>emoji_nature</mi>
          <p title>Appeal</p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <!--- Restriction content --->
          <box-model outlined=darker rounded>
            <bm-inr size=std>
              <div fl gap alic>
                <mi size=mid>raven</mi>
                <div>
                  <div fl alic gap=smol>
                    <p text bold>Restriction</p>
                    &middot;
                    <p text color=company><?= Time::ago($Restriction->created_at); ?></p>
                  </div>
                  <p text><?= $Restriction->reason ?? "No reason provided"; ?></p>
                </div>
              </div>
            </bm-inr>
          </box-model>

          <div fl fldircol gap=smol+>
            <div fl fldircol gap=smolest>
              <p text bold std>Content</p>
              <p text std slight>Why did you get restricted in your opinion and what would you like to change in the
                future</p>
            </div>
            <div textarea appear=lighter>
              <textarea <?= $Appeal ? "readonly slight" : "autofocus"; ?> auto-resize material name="content" placeholder="Write something..." tabindex=2><?= $Appeal->content ?? ""; ?></textarea>
            </div>
          </div>

          <?php if (!$Appeal || !$Appeal->live_play_file) { ?>
            <div fl fldircol gap=smol+>
              <div>
                <div fl alic gap=smol>
                  <p text bold>Live-Play (YouTube-Link)</p>
                  <div pblock6 pinline2 rounded background=company color=light>
                    <p text smol ttup>optional</p>
                  </div>
                </div>
                <p text slight>Attach a link to a live play you have uploaded to YouTube, to get whitelisted and be
                  protected from
                  automated restrictions</p>
              </div>
              <div fl fldircol gap=smol+>
                <div input material has-icon flexone appear=lighter>
                  <mi>link</mi>
                  <input type="text" name="live_play_file" placeholder="https://youtu.be/xxx" />
                </div>
                <div fl alistart gap=smol+ slight>
                  <mi std style=padding-top:.2em;>info</mi>
                  <p text>
                    Find out, how to create an
                    appropriate live play <a extern target="_blank" href="https://discord.gg/f4SXUabd6n" normal>here</a>
                  </p>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <div fl fldircol gap=smol+>
              <p text bold>Submitted live play</p>

              <div fl gap=smol>
                <div input material has-icon flexone appear=lighter disabled>
                  <mi>link</mi>
                  <input type="text" value="<?= $Appeal->live_play_file; ?>" />
                </div>
                <a extern href="<?= $Appeal->live_play_file; ?>" target="_blank">
                  <mbutton material size=mid outlined=darker circled icon-only has-tooltip=bottom>
                    <mi>play_arrow</mi>
                    <div ttooltip>
                      <p text bold>View in new tab</p>
                    </div>
                  </mbutton>
                </a>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <div prompt-actions>
        <mbutton material background=clean close-overlay>
          <p text>Cancel</p>
        </mbutton>

        <?php if ($is_editable) { ?>
          <mbutton submit-closest size=mid material has-icon=left background=<?= $Appeal ? "refollow" : "follow"; ?> color=dark>
            <?php if ($Appeal) { ?>
              <mi>refresh</mi>
            <?php } else { ?>
              <mi>done</mi>
            <?php } ?>
            <p text bold><?= $Appeal ? "Update" : "Submit"; ?></p>
          </mbutton>
        <?php } else echo "<div></div>"; ?>
      </div>
    </box-model>

  </div>
</form>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
