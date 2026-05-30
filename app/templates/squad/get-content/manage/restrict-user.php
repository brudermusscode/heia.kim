<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Squad\SquadUser;
use Bruder\Time\Time;

/**
 * @var User $CurrentUser
 * @var Request $Request
 */

/**
 * @var ?SquadUser
 */
$CurrentSquadUser = $CurrentUser->squad_user;

/**
 * User has permissions?
 * ! Error
 */
if (!$CurrentSquadUser || !$CurrentSquadUser->can("coordinate", "users"))
  exit($Request->error("!NO_PERMISSIONS"));

/**
 * @var Squad
 */
$Squad = $CurrentSquadUser->squad;

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?User
 */
$User = User::find($id);

/**
 * User doesn't exist or is not a member of this squad?
 * ! Error
 */
if (
  !$User
  || !$User->squad
  || !$User->squad()->is($Squad)
  || (
    $User->squad_user->has_elevated_privileges()
    && !$CurrentUser->is_squad_chief()
  )
)
  exit($Request->error());

/**
 * @var SquadUser
 */
$SquadUser = $User->squad_user;

/**
 * User is owner?
 * ! Error
 */
if ($User->squad_user->is_owner())
  exit($Request->error("!NO_PERMISSIONS"));

/**
 * Begin output buffer.
 */
ob_start();

include SNOW; ?>

<form request="squad:user:update" reload responder>
  <div content-width=smolest prompt-height>

    <input type=hidden name=id value=<?= $User->id; ?> />
    <input type=hidden name=clan_priv value=2 />

    <box-model prompt elevated rounded=wide filled=lighter>
      <div prompt-content>
        <div prompt-header>
          <mi>front_hand</mi>
          <p title>Restrict Member</p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <a href="<?= $User->link(); ?>">
            <box-model outlined=darker rounded clickable>
              <bm-inr size=std fl alic gap=smol+>
                <picture size=midler circled>
                  <?php $User->image(); ?>
                </picture>
                <div>
                  <p text midler bold><?= $User->name; ?></p>
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
            <p text bold>What it does</p>
            <p text>
              The member will be excluded from all community functions inside this squad. They won't be able to contribute to the performance of any gamemode active.
            </p>
          </div>

          <tipp-box outlined=darker rounded=mid>
            <mi>info</mi>
            <p text>The player will get notified about their set limit.</p>
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
