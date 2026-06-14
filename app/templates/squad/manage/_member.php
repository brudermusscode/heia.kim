<?php

use Heiakim\Enum\SquadPrivilege;
use Heiakim\Model\User;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Time\Time;

/**
 * @var User $User
 * @var SquadUser $Member
 */

?>

<box-model squad-member posrel clickable>
  <a href="<?= $User->link(); ?>">
    <bm-inr size=smoler fl gap>
      <picture size=mid circled ovhid rounded=mid>
        <?= $User->image(); ?>
      </picture>

      <div fl alic flexone jucsb>
        <div>
          <p text bold><?= $User->name; ?></p>
          <div fl gap=smoler alic>
            <p text>Last active &middot; <span color=company><?= Time::ago($User->latest_activity); ?></span></p>
          </div>
        </div>
      </div>
    </bm-inr>
  </a>
  <?php

  /**
   * Include actions only for non chiefs.
   */
  if (!$Member->is_owner()) { ?>
    <div box-floating-actions fl gap=smoler alic jucend>
      <?php if (CurrentUser->squad_user->can("manage", "users")) { ?>
        <mbutton
          request-get="squad:get-content:manage:promote-user"
          data-id="<?= $Member->id; ?>"
          outlined=darker icon-only filled=lighter has-tooltip=bottom>
          <mi bold>keyboard_double_arrow_up</mi>
          <div ttooltip>
            <p text bold>Promote</p>
          </div>
        </mbutton>
      <?php } ?>

      <?php

      if (
        CurrentUser->sqcan("coordinate", "users")
        && !$Member->has_elevated_privileges()
        || CurrentUser->is_squad_chief()
      ) : ?>

        <?php if (!$Member->is_restricted()) { ?>
          <mbutton
            request-get="squad:get-content:manage:restrict-user"
            data-id="<?= $User->id; ?>"
            icon-only background=besure color=dark-orange has-tooltip=bottom>
            <mi>front_hand</mi>
            <div ttooltip>
              <p text bold>Restrict</p>
            </div>
          </mbutton>
        <?php } else { ?>
          <form request="squad:user:update" reload>
            <input type=hidden name=id value=<?= $User->id; ?> />
            <input type=hidden name=clan_priv[<?= SquadPrivilege::MEMBER->value; ?>] value="1" />
            <input type=hidden name=clan_priv[<?= SquadPrivilege::UNRESTRICTED->value; ?>] value="1" />
            <mbutton icon-only background=follow color=dark-green submit-closest has-tooltip=bottom>
              <mi>check</mi>
              <div ttooltip>
                <p text bold>Set free</p>
              </div>
            </mbutton>
          </form>
        <?php } ?>
      <?php endif; ?>

      <?php if (CurrentUser->sqcan("manage", "users")) : ?>
        <div has-inner-prompt=left>
          <form request="squad:user:kick" posrel responder <?= CurrentUser->is($User) ? "" : "reload"; ?>>
            <input type=hidden name=id value=<?= $Member->id; ?> />
            <div filled=lighter rounded=mid elevated=wide prompt inner-prompt>
              <div prompt-content fl fldircol gap>
                <div prompt-inner-content>
                  <div prompt-header fl fldircol gap=smoler>
                    <p text midler bold>Are you sure?</p>
                  </div>
                  <?php if (CurrentUser->is($User)) { ?>
                    <p text><strong>When removing yourself</strong> from the squad, you risk a deplacement for it on the leaderboards.</p>
                  <?php } else { ?>
                    <p text>When removing this user, you risk a deplacement of your squad.</p>
                  <?php } ?>
                </div>
              </div>

              <div prompt-actions>
                <div></div>
                <mbutton background=slight submit-closest>
                  <p text bold>I am!</p>
                </mbutton>
              </div>
            </div>

            <mbutton open-inner-prompt icon-only background=unfollow color=dark-red has-tooltip=bottom>
              <mi>remove</mi>
              <div ttooltip>
                <p text bold>Remove</p>
              </div>
            </mbutton>
          </form>
        </div>
      <?php endif; ?>

    </div>
  <?php } ?>

</box-model>