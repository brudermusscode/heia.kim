<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Time\Time;
use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var Request $Request
 */

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
$Member = SquadUser::findOrReturn($id, "<strong>Member doesn't exist lol.</strong>");

/**
 * @var User
 */
$User = $Member->user;

/**
 * CurrentUser has permissions?
 */
// TODO: Automatically exit on error inside GET templates.
if (!CurrentUser->squad_user->can_edit_permissions_of($Member))
  exit($Request->error("!NO_PERMISSIONS"));

/**
 * Begin output buffer.
 */
ob_start();

include SNOW; ?>

<form request="squad:user:update" reload responder>
  <div content-width=smolest prompt-height>

    <input type=hidden name=id value=<?= $User->id; ?> />

    <box-model prompt elevated rounded=wide filled=lighter>
      <div prompt-content>
        <div prompt-header>
          <mi>shield_toggle</mi>
          <p title>Edit Permissions</p>
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
              <mbutton curpo tag filled=lighter icon-only arrow-further="right"
                style="right:1.2em;">
                <mi midler>east</mi>
              </mbutton>
            </box-model>
          </a>

          <div fl fldircol gap=smol p12>
            <?php

            /**
             * @var array[SquadPrivilege]
             */
            $Privileges = $Member->privileges();

            foreach (SquadPrivilege::cases() as $Privilege) {

              /**
               * Don't show certain SquadPrivileges
               */
              if (in_array($Privilege, [SquadPrivilege::UNRESTRICTED, SquadPrivilege::MEMBER]))
                continue;

              /**
               * @var object
               */
              $privilege_display = $Privilege->get_display();

              /**
               * @var bool
               */
              $has_privileges = $Member->has_privileges_of($Privilege);

            ?>

              <div expand-more fl fldircol gap=smol>
                <div fl jucsb alic gap>
                  <div fl alic gap=smol+>
                    <div filled=darker circled style="height:2.4em;width:2.4em;" fl jucc alic>
                      <mi><?= $privilege_display->icon; ?></mi>
                    </div>
                    <div>
                      <p text bold><?= $privilege_display->name; ?></p>
                      <?php if ($Privilege !== SquadPrivilege::MEMBER) { ?>
                        <p text smol><?= $privilege_display->tasks; ?></p>
                      <?php } ?>
                    </div>
                  </div>

                  <?php

                  /**
                   * @var bool
                   */
                  $missing_permissions_disable = !CurrentUser->is_squad_chief()
                    && in_array($Privilege, [SquadPrivilege::CHIEF, SquadPrivilege::COMMUNITY_MANAGER])
                    || $Privilege === SquadPrivilege::CHIEF;

                  ?>

                  <div fl alic gap=smoler>
                    <toggle-switch
                      <?php if ($missing_permissions_disable) echo "disabled"; ?>
                      toggled="<?= $has_privileges ? "true" : "false"; ?>">
                      <div class="toggle_switch__inr">
                        <div class="toggle_switch__switcher"></div>
                        <?php if (!$missing_permissions_disable) : ?>
                          <input type="hidden" name="clan_priv[<?= $Privilege->value; ?>]" value="<?= $has_privileges ? "1" : "0" ?>">
                        <?php endif; ?>
                        <div fl fldirrow justify-content="center">
                          <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                          </div>
                        </div>
                      </div>
                    </toggle-switch>
                    <mbutton icon-only hoverable expand-more-show has-tooltip=left>
                      <mi>info</mi>
                      <div ttooltip>
                        <p text bold>Show full description</p>
                      </div>
                    </mbutton>
                  </div>
                </div>

                <box-model outlined=darker rounded=mid expand-more-hidden fl fldircol gap=smoler>
                  <bm-inr size=smol>
                    <p text><?= $privilege_display->full_tasks; ?></p>
                  </bm-inr>
                </box-model>
              </div>

            <?php } ?>
          </div>

          <tipp-box outlined=darker rounded=mid>
            <mi>info</mi>
            <p text>The player will get notified about their promotion or derank.</p>
          </tipp-box>
        </div>
      </div>

      <div prompt-actions>
        <mbutton close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <mbutton mid submit-closest>
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
