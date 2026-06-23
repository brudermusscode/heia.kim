<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var ?SquadFeedItem
 */
$Logs = $Squad
  ->logs
  ->filter(fn($log) => $log->type !== "__post__")
  ->sortByDesc("created_at");

?>

<div fl alic gap>
  <?php include TEMPLATE . "/squad/manage/_back-button.php"; ?>
  <p text mid bold>Logs</p>
</div>

<div fl fldircol>
  <table style="width: 100%; table-layout: fixed;">
    <tr background=slighter rounded>
      <td colspan="2" text bold>Triggered</td>
      <td colspan="2" text bold>Type</td>
      <td colspan="2" text bold>Affected</td>
      <td colspan="2" text bold>Date</td>
      <td colspan="1" text bold fl jucend>
        <mi color=company>process_chart</mi>&nbsp;
      </td>
    </tr>

    <?php

    foreach ($Logs->take(32) as $Log) {

      /**
       * @var SquadFeedItem $Log
       */

      /**
       * @var User
       */
      $TriggeredUser = $Log->user;

      /**
       * @var ?User
       */
      $AffectedUser = $Log->affected_user ?? User::guest();

      /**
       * @var bool
       */
      $is_same_user = $TriggeredUser->is($AffectedUser);

    ?>

      <tr hoverable background=transparent style="cursor:default;border-radius:0;border-bottom:1px solid rgba(0,0,0,.04);">

        <!--- Triggered User --->
        <td colspan=2>
          <a href="/u/<?= $TriggeredUser->id ?>" fl jucstart>
            <div rounded=wide filled clickable style="padding:8px 16px 8px 8px;" fl alic gap=smol>
              <picture size=smol circled posrel>
                <?php $TriggeredUser->image(); ?>
              </picture>
              <p text><?= $TriggeredUser->name() ?></p>
            </div>
          </a>
        </td>

        <!--- Triggered User --->
        <td colspan=2>
          <p text trimt fl alic gap=smol>
            <mi color=company><?= $Log->display_type()->icon ?></mi>
            <?= $Log->display_type()->append_text ?>
          </p>
        </td>

        <!--- Triggered User --->
        <td colspan=2>
          <div fl jucstart>
            <?php if (!$is_same_user && $AffectedUser->exists) : ?>
              <a href="/u/<?= $AffectedUser->id ?>">
                <div rounded=wide filled clickable style="padding:8px 16px 8px 8px;" fl alic gap=smol>
                  <picture size=smol circled posrel>
                    <?php $AffectedUser->image(); ?>
                  </picture>
                  <p text><?= $AffectedUser->name() ?></p>
                </div>
              </a>
            <?php else : ?>
              <p text slighter style="font-style: italic;">………</p>
            <?php endif; ?>
          </div>
        </td>

        <td colspan=2>
          <p text smol slight no-word-wrap><?= date("d. M Y", strtotime($Log->created_at)) ?></p>
        </td>

        <td colspan=1 fl jucend>
          <div posrel menu-outer>
            <mbutton outlined icon-only ripple-effect open-more-menu
              has-tooltip="bottom">
              <mi>more_vert</mi>
              <div ttooltip>
                <p text std bold>More options</p>
              </div>
            </mbutton>

            <jump-menu menu-more filled="lighter" elevated color="dynamic">
              <?php if ($TriggeredUser && $TriggeredUser->squad?->is($Squad)) : ?>

                <?php if (CurrentUser->squad_user->can_restrict($TriggeredUser->squad_user)) : ?>
                  <form request="squad:user:update" reload responder>
                    <input type=hidden name=id value=<?= $TriggeredUser->id ?>>
                    <input type=hidden name=clan_priv value=2>
                    <div ripple-effect class="jm__option" hoverable submit-closest>
                      <mi>front_hand</mi>
                      <p text std>Restrict <?= $TriggeredUser->name() ?></p>
                    </div>
                  </form>
                <?php endif; ?>

                <div divide="line"></div>

              <?php endif; ?>

              <?php if ($AffectedUser->exists && $AffectedUser->squad?->is($Squad)) : ?>

                <?php if (CurrentUser->squad_user->can_restrict($AffectedUser->squad_user)) : ?>
                  <form request="squad:user:update" reload responder>
                    <input type=hidden name=id value=<?= $AffectedUser->id ?>>
                    <input type=hidden name=clan_priv value=2>
                    <div ripple-effect class="jm__option" hoverable submit-closest>
                      <mi>front_hand</mi>
                      <p text std>Restrict <?= $AffectedUser->name() ?></p>
                    </div>
                  </form>
                <?php endif; ?>

              <?php endif; ?>
            </jump-menu>
          </div>
        </td>

      </tr>

    <?php } ?>
  </table>
</div>