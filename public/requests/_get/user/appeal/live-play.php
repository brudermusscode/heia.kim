<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Application\Application;
use Heiakim\Time\Time;
use Heiakim\Model\User;

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
 * User not restricted nor frozen?
 * ! Error
 */
if (!FROZEN)
  exit($Request->error("!FROZEN"));

/**
 * The user should not be able to view the live play submission
 * dialogue when being restricted. So return an error if so.
 * ! Error
 */
if (RESTRICTED)
  exit($Request->error("!RESTRICTED"));

/**
 * @var ?DateTime
 */
$freeze_ends = FROZEN ? (new DateTime($CurrentUser->frozen_at))->modify("+5 days") : null;

/**
 * @var ?Restriction
 */
$Restriction = $CurrentUser->current_restriction();

/**
 * @var ?RestrictionAppeal
 */
$Appeal = $CurrentUser->current_appeal();

/**
 * An appeal exists and there is a live play attached to it.
 *
 * @var bool
 */
$reviewing_live_play =
  $Appeal
  && $Appeal->status === "AWAITING_PROCESSING"
  && $Appeal->live_play_file;

/**
 * Begin output buffer.
 */
ob_start();

/**
 * The snow effect of love.
 */
include SNOW;

?>

<form data-form="users:appeal,create">
  <div content-width=smolest prompt-height>

    <box-model prompt filled elevated>
      <div prompt-content>
        <div prompt-header>
          <mi>live_tv</mi>
          <p title><?= $reviewing_live_play ? "View" : "Add"; ?> Live Play</p>
        </div>

        <div prompt-inner-content>
          <div fl fldircol gap>
            <?php if (!$reviewing_live_play) { ?>
              <p text std>If you think your current account state is unjustified, please read along in our <a href="https://discord.gg/f4SXUabd6n" normal extern target="_blank">Info-channel on Discord</a> about how
                to create an
                appropriate live
                play.</p>
            <?php } ?>

            <div fl fldircol gap>
              <div fl fldircol gap=smol+>

                <?php if (FROZEN && !RESTRICTED) { ?>
                  <box-model outlined=darker rounded>
                    <bm-inr size=std>
                      <div fl gap alic>
                        <mi size=mid>rainy_snow</mi>
                        <div>
                          <div fl alic gap=smol>
                            <p text bold>Freeze</p>
                            &middot;
                            <p text color=company><?= Time::ago($CurrentUser->frozen_at); ?></p>
                          </div>
                        </div>
                      </div>
                    </bm-inr>
                  </box-model>
                <?php } ?>

                <?php if (RESTRICTED) { ?>
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
                <?php } ?>
              </div>

              <?php if (!$reviewing_live_play) { ?>

                <div fl fldircol gap=smol>
                  <p text bold>YouTube link</p>
                  <div fl fldircol gap=smol+>
                    <div input material has-icon flexone appear=lighter>
                      <mi>link</mi>
                      <input type="text" autofocus name="live_play_file" placeholder="https://youtu.be/xxx" enter-submitable />
                    </div>
                    <div fl alistart gap=smol+ slight>
                      <mi std style=padding-top:.2em;>info</mi>
                      <p text>
                        Please only use a link that <strong>directly and obviously leads to YouTube</strong>. Any other link
                        will be declined.
                      </p>
                    </div>
                  </div>
                </div>

                <div fl jucend>

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
                      <mbutton material size=mid outlined=darker circled icon-only>
                        <mi>play_arrow</mi>
                      </mbutton>
                    </a>
                  </div>
                </div>

                <div fl alistart gap=smol+ slight>
                  <mi std style=padding-top:.2em;>info</mi>
                  <p text>Your live play is currently being reviewed. Please wait for us to process it and reach out to you.
                  </p>
                </div>

              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <div prompt-actions>
        <mbutton material background=clean close-overlay>
          <p text>Cancel</p>
        </mbutton>

        <?php if (!$reviewing_live_play) { ?>
          <mbutton background=slight material size="mid" submit-closest>
            <p text bold>Submit</p>
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
