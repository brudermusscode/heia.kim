<?php

use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var Squad $Squad
 */

?>

<div content-width=mid>

  <div fl align-items="center" gap=mid mb mt=wide>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>
    <p text mid bold>Members</p>
  </div>

  <table style="width: 100%; table-layout: fixed;">

    <tr background=slighter rounded>
      <td colspan="2" text bold>Player</td>
      <td colspan="2" text bold>Staff grade</td>
      <td colspan="1" text bold>Country</td>
      <td colspan="2" text bold>Joined</td>
      <td colspan="1" text bold fl jucend>
        <mi color=company>process_chart</mi>&nbsp;
      </td>
    </tr>

    <?php

    /**
     * @var SquadUser
     */
    $Members = $Squad->members
      ->sortByDesc("clan_priv")
      ->take(32);

    foreach ($Members as $Member) {

      /**
       * @var SquadUser $Member
       */

    ?>

      <tr hoverable background=transparent style="cursor:default;border-radius:0;border-bottom:1px solid rgba(0,0,0,.04);">

        <td colspan=2>
          <a href="/u/<?= $Member->user_id ?>">
            <div fl alic gap=smol+ jucstart>
              <picture size=std circled posrel>
                <img src="<?= AVATAR . "/$Member->user_id" ?>" />
              </picture>
              <p text><?= $Member->user->name() ?></p>
            </div>
          </a>
        </td>

        <td colspan=2>
          <p text trimt fl alic gap=smol>
            <mi color=company><?= $Member->highest_privileges()->icon ?></mi>
            <?= $Member->highest_privileges()->name ?>
          </p>
        </td>

        <td colspan=1>
          <picture style=height:1.2em;width:1.8em; rounded=smol ovhid><?php $Member->user->country_icon() ?></picture>
        </td>

        <td colspan=2>
          <p text smol slight no-word-wrap><?= date("d. M Y", strtotime($Member->created_at)) ?></p>
        </td>

        <td colspan=1 fl jucend>
          <?php if (!$Member->user->is(CurrentUser) && (!$Member->can("manage", "users") || CurrentUser->is_squad_chief())) : ?>
            <div posrel menu-outer>
              <mbutton material outlined icon-only ripple-effect open-more-menu has-tooltip="bottom">
                <mi>more_vert</mi>
                <div ttooltip>
                  <p text std bold>More options</p>
                </div>
              </mbutton>

              <jump-menu menu-more filled="lighter" elevated color="dynamic">

                <?php

                /**
                 * @var int
                 */
                $menu_option_count = 0;

                /**
                 * @var bool
                 */
                $can_manage_user_updates = CurrentUser->squad_user->can_edit_permissions_of($Member);

                /**
                 * ? Edit permissions
                 */
                if ($can_manage_user_updates) : ?>
                  <div
                    request-get="squad:get-content:manage:promote-user"
                    data-id="<?= $Member->id ?>"
                    ripple-effect class="jm__option" hoverable submit-closest>
                    <mi>shield_toggle</mi>
                    <p text std>Edit permissions</p>
                  </div>

                  <div divide="line"></div>
                <?php $menu_option_count++;
                endif; ?>

                <?php

                /**
                 * ? Restrict/Set free
                 */
                if (CurrentUser->squad_user->can_restrict($Member)) : ?>
                  <?php if (!$Member->is_restricted()) : ?>
                    <div
                      request-get="squad:get-content:manage:restrict-user"
                      data-id="<?= $Member->user_id ?>"
                      ripple-effect class="jm__option" hoverable submit-closest>
                      <mi>front_hand</mi>
                      <p text std>Restrict</p>
                    </div>
                  <?php else : ?>
                    <form request="squad:user:update" reload responder>
                      <input type=hidden name=id value=<?= $Member->user_id; ?> />
                      <input type=hidden name=clan_priv[<?= SquadPrivilege::MEMBER->value; ?>] value="1" />
                      <input type=hidden name=clan_priv[<?= SquadPrivilege::UNRESTRICTED->value; ?>] value="1" />
                      <div ripple-effect class="jm__option" hoverable submit-closest color=green>
                        <mi>crowdsource</mi>
                        <p text std>Set free</p>
                      </div>
                    </form>
                  <?php endif; ?>
                <?php $menu_option_count++;
                endif; ?>

                <?php

                /**
                 * ? Kick
                 */
                if ($can_manage_user_updates) : ?>
                  <div
                    request-get="squad:get-content:manage:kick-user"
                    data-id="<?= $Member->id ?>"
                    ripple-effect class="jm__option" hoverable submit-closest color=red>
                    <mi>sports_martial_arts</mi>
                    <p text std>Kick from squad</p>
                  </div>
                <?php $menu_option_count++;
                endif; ?>

                <?php if (!$menu_option_count) : ?>
                  <p pblock12 text tac slight style="font-style: italic;">Nothing here</p>
                <?php endif; ?>
              </jump-menu>
            </div>
          <?php else : ?>
            <mbutton material icon-only disabled filled>
              <mi>heart_smile</mi>
            </mbutton>
          <?php endif; ?>
        </td>

      </tr>

    <?php } ?>
  </table>

</div>