<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Time\Time;

/**
 * @var Request $Request
 */

/**
 * User has permissions?
 */
if (!CurrentUser?->sqcan("manage", "users"))
  exit($Request->error("!NO_PERMISSIONS"));

/**
 * @var Squad
 */
$Squad = CurrentUser->squad;

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?SquadUser
 */
$Member = SquadUser::findOrReturn($id, "<strong>No member found kurwa!</strong>");

/**
 * @var User
 */
$User = $Member->user;

/**
 * Check up on permissions of the user.
 */
if (
  $User->is(CurrentUser)
  || CurrentUser->is_squad_chief() && $User->is_squad_chief()
  || !CurrentUser->is_squad_chief()
  && (
    $Member->can("manage", "users", in: $Squad)
  )
)
  exit($Request->error("!NO_PERMISSIONS"));

/**
 * Begin output buffer.
 */
ob_start();

include SNOW; ?>

<form request="squad:user:kick" reload responder>
  <div content-width=smolest prompt-height>
    <input type=hidden name=id value=<?= $Member->id; ?> />

    <box-model prompt elevated rounded=wide filled=lighter>
      <div prompt-content>
        <div prompt-header>
          <mi>sports_martial_arts</mi>
          <p title>Kick Member</p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <a href="<?= $User->link(); ?>">
            <box-model outlined=darker rounded clickable>
              <bm-inr size=std fl alic gap=smol+>
                <picture size=midler circled>
                  <?php $User->image(); ?>
                </picture>
                <div>
                  <p text midler bold><?= $User->name(); ?></p>
                  <p text smol>
                    Last active &middot; <span color=company><?= "" . Time::ago($User->latest_activity, true); ?></span>
                  </p>
                </div>
              </bm-inr>
              <mbutton material curpo tag filled=lighter icon-only arrow-further="right" style="right:1.2em;">
                <mi midler>east</mi>
              </mbutton>
            </box-model>
          </a>

          <div fl fldircol gap=smoler>
            <p text bold>Are you sure?</p>
            <p text>
              Kicking the member will permanently disband them from your squad and restrict them to join for a fixed amount of time.
            </p>
          </div>

          <tipp-box outlined=darker rounded=mid>
            <mi>info</mi>
            <p text>The player will get notified.</p>
          </tipp-box>
        </div>
      </div>

      <div prompt-actions>
        <mbutton material close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <mbutton background=besure color=dark-yellow size=mid material submit-closest>
          <p text std bold>Confirm</p>
        </mbutton>
      </div>
    </box-model>

  </div>
</form>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
