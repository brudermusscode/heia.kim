<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Restriction\Restriction;
use Bruder\Heiakim\Model\Restriction\RestrictionAppeal;
use Bruder\Time\Time;

/**
 * @var User $CurrentUser
 */

/**
 * User is not restricted?
 */
if (!RESTRICTED && !FROZEN)
  include UNAVAILABLE;
else {

  /**
   * @var ?Restriction
   */
  $Restriction = $CurrentUser->current_restriction();

  /**
   * @var int
   */
  $restrictions_count = $CurrentUser->restrictions()->count();

  /**
   * @var string
   */
  $appeal_interval = match ($restrictions_count) {
    1 => "+1 month",
    2 => "+5 months",
    default => "+1 year",
  };

  /**
   * @var ?string
   */
  $is_frozen = FROZEN;

  /**
   * @var string
   */
  $frozen_at = $CurrentUser->frozen_at;

  /**
   * @var ?DateTime
   */
  $freeze_ends = $is_frozen ? (new DateTime($CurrentUser->frozen_at))->modify("+5 days") : null;

  /**
   * @var ?string
   */
  $freeze_time_left = $freeze_ends ? Time::left($freeze_ends->format("Y-m-d H:i:s")) : null;

  /**
   * @var ?RestrictionAppeal
   */
  $Appeal =
    $CurrentUser->current_appeal()
    ?? $CurrentUser->current_appeal_after_restriction_waiting_period();

  /**
   * @var bool
   */
  $reviewing_appeal = $CurrentUser->appeal_being_reviewed();

?>

  <div mt=wide mb fl gap=mid align-items="center" mb=std>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold>Restriction</p>
      </div>
    </label>
  </div>

  <div progress-list fl fldircol gap=mid posrel mt=wide>

    <div class=pl-line filled=lighter rounded></div>



    <!--- FROZEN --->
    <div fl gap alistart class=pl-option>
      <div class=pl-icon outlined fl jucc alic circled>
        <mi midler>rainy_snow</mi>
      </div>

      <box-model class=pl-box filled flexone>
        <bm-inr size=mid>
          <div fl fldircol gap=smol+>
            <p text std>
              <span slight>Account frozen</span>
              &middot;
              <span color=company>
                <?= Time::ago($frozen_at, true); ?>
              </span>
            </p>
            <p text std>Due to <strong>suspected violation of our <a extern target="_blank"
                  href="https://discord.gg/QCA2GVWE3w" normal>community rules</a></strong>, we have set your account to
              frozen
              mode. You have some days to send us a live play till your account will be
              set to
              restricted mode.
            </p>

            <?php if (!$Appeal && !$CurrentUser->current_appeal_declined()) { ?>
              <?php if ($freeze_time_left && !RESTRICTED) { ?>
                <p text midler bold><strong><?= Time::left($freeze_ends->format("Y-m-d H:i:s"), true); ?></strong></p>

                <div fl alic gap=smol+ slight mt>
                  <mi std>info</mi>
                  <p text>
                    <span>Find out, how to create an appropriate live play</span> <a extern target="_blank"
                      href="https://discord.gg/f4SXUabd6n" normal>here</a>.
                  </p>
                </div>
              <?php } else { ?>
                <p text midler bold><strong>Freeze time has passed</strong></p>
              <?php } ?>
            <?php } ?>
          </div>
        </bm-inr>
      </box-model>
    </div>

    <?php

    /**
     * User is not yet restricted.
     */
    if (!$CurrentUser->is_restricted()) { ?>

      <div fl gap alistart>
        <div class=pl-icon outlined fl jucc alic circled>
          <mi midler>slow_motion_video</mi>
        </div>

        <?php if ($Appeal && $Appeal->status === "AWAITING_PROCESSING") { ?>

          <div fl flexone jucc mt=smol>
            <mbutton background=slight color=dynamic material size=mid has-icon=left data-action="popup:open"
              data-href="/user/appeal/live-play" has-tooltip=bottom>
              <mi class="loader-pulse posrel" fl jucstart alic style="height:32px;width:32px;top:0;">
                <span></span>
              </mi>
              <p text bold>Reviewing live play</p>
              <div ttooltip>
                <p text bold>View</p>
              </div>
            </mbutton>
          </div>

        <?php } else if ($Appeal && $Appeal->status === "REDO_REQUESTED") { ?>

          <box-model class=pl-box filled flexone>
            <bm-inr size=mid>
              <div fl fldircol gap=smol+>

                <div fl jucc fldircol gap alic style="margin-bottom:-6.2em;margin-top:-2.4em;">
                  <dotlottie-player src="https://lottie.host/47eb7ab7-ab5c-4d04-aa93-0610dacc15be/0Xwz8DMccv.json"
                    background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
                </div>

                <div fl fldircol tac mb>
                  <p text midler bold>
                    Your live play was inappropriate!
                  </p>
                  <p text std>
                    Find out, how to create an appropriate live play <a extern target="_blank"
                      href="https://discord.gg/f4SXUabd6n" normal>here</a>
                  </p>
                </div>

                <div flexone fl jucc>
                  <mbutton background=follow color=dark-green material size=mid has-icon=left data-action="popup:open"
                    data-href="/user/appeal/live-play">
                    <mi>add</mi>
                    <p text bold>Add new</p>
                  </mbutton>
                </div>
              </div>
            </bm-inr>
          </box-model>

        <?php } else { ?>

          <div fl jucc flexone>
            <mbutton background=follow color=dark-green material size=mid has-icon=left data-action="popup:open"
              data-href="/user/appeal/live-play">
              <mi>add</mi>
              <p text bold>Add live play</p>
            </mbutton>
          </div>

        <?php } ?>
      </div>

    <?php } else { ?>

      <div fl gap alic>
        <div class=pl-icon-hidden-section filled=lighter fl jucc alic rounded>
          <mi midler>steppers</mi>
        </div>

        <div fl jucc flexone></div>
      </div>

    <?php } ?>




    <!--- AUTOMATIC RESTRICTION LOCKED --->
    <div fl gap <?php if (!$CurrentUser->is_restricted()) echo "slighter"; ?>>
      <div class=pl-icon outlined fl jucc alic circled>
        <mi midler>raven</mi>
      </div>

      <?php if (!$CurrentUser->is_restricted()) { ?>

        <p text flexone tac pinline24>
          <?php if (!$Appeal) { ?>
            Account will be restricted on
            <strong><?= $freeze_ends->format("j. F Y"); ?></strong>
          <?php } else { ?>
            Automated restriction is locked
          <?php } ?>
        </p>



        <!--- RESTRICTED --->
      <?php

      } else {

        /**
         * @var Restriction
         */
        $Restriction = $CurrentUser->current_restriction();

      ?>

        <box-model filled flexone>
          <bm-inr size=mid>
            <div fl fldircol gap=smol>
              <p text std>
                <span slight>Account restricted</span>
                &middot;
                <span color=company>
                  <?= Time::ago($Restriction->created_at, true); ?>
                </span>
              </p>
              <p text bold midler><?= $Restriction->reason ?? "No reason provided"; ?></p>
            </div>
          </bm-inr>
        </box-model>

      <?php } ?>
    </div>



    <!--- APPEAL NOW --->
    <?php if ($CurrentUser->is_restricted()) { ?>
      <div fl gap alistart>
        <div class=pl-icon outlined fl jucc alic circled>
          <mi midler>rate_review</mi>
        </div>

        <div fl jucc flexone>

          <?php

          /**
           * @var ?RestrictionAppeal
           */
          $DeclinedAppeal = $CurrentUser->declined_appeal();

          if (!$DeclinedAppeal) { ?>

            <?php

            /**
             * @var ?string
             */
            $appeal_locked = $CurrentUser->appeal_locked();

            if ($appeal_locked) { ?>
              <mbutton background=follow color=dark-green material size=mid has-icon=left disabled>
                <mi>history_toggle_off</mi>
                <p text bold>Appeal locked for <?= $appeal_locked; ?></p>
              </mbutton>
            <?php } else { ?>
              <?php if (!$CurrentUser->current_appeal_after_restriction_waiting_period()) { ?>
                <mbutton background=follow color=dark-green material size=mid has-icon=left data-action="popup:open"
                  data-href="/user/appeal/create">
                  <mi>edit</mi>
                  <p text bold>Write an appeal</p>
                </mbutton>
              <?php } else { ?>
                <mbutton background=slight color=dynamic material size=mid has-icon=left data-action="popup:open"
                  data-href="/user/appeal/create" has-tooltip=bottom>
                  <mi class="loader-pulse posrel" fl jucstart alic style="height:32px;width:32px;top:0;">
                    <span></span>
                  </mi>
                  <p text bold>Reviewing appeal</p>
                  <div ttooltip>
                    <p text bold>View current appeal</p>
                  </div>
                </mbutton>
              <?php } ?>
            <?php } ?>
          <?php } else { ?>
            <box-model filled flexone>
              <bm-inr size=mid>
                <div fl fldircol gap=smol+>
                  <p text std>
                    <span slight>Appeal declined</span>
                    &middot;
                    <span color=company>
                      <?= Time::ago($DeclinedAppeal->updated_at, true); ?>
                    </span>
                  </p>
                  <p text>We will reach out to you, if your account has any chance of getting unrestricted at some
                    point. By now, there is none.</p>
                </div>
              </bm-inr>
            </box-model>
          <?php } ?>
        </div>
      </div>
    <?php } ?>

  </div>

<?php } ?>