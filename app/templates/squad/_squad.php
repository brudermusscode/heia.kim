<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Gamemode;
use Bruder\Heiakim\Model\Squad;

/**
 * @var User $CurrentUser
 * @var Squad $Squad
 */

/**
 * Temporarily set this to update all squads.
 */
$Squad->update_performance();

/**
 * @var bool
 */
$set_appart ??= false;

/**
 * @var bool
 */
$is_my_clan = $CurrentUser->squad?->is($Squad);

if ($is_my_clan) {
  $pub_text = __("Your squad");
  $publicity = <<< TEXT
    <div class=joinable filled circled has-tooltip=bottom>
      <mi>check</mi>
      <div ttooltip>
        <p text std bold>$pub_text</p>
      </div>
    </div>
  TEXT;
} else if (
  LOGGED && $CurrentUser->squad_requests()
  ->where("clan_id", $Squad->id)
  ->where("type", "join")
  ->whereNull("deleted_at")
  ->first()
) {
  $pub_text = __("Your request is pending");
  $publicity = <<< TEXT
    <div class=joinable background=refollow color=dark-blue circled has-tooltip=bottom>
      <p class="loader-pulse">
        <span></span>
      </p>
      <div ttooltip>
        <p text std bold>$pub_text</p>
      </div>
    </div>
  TEXT;
} else {
  switch ($Squad->joinable) {
    case 2:
      $pub_text = __("Public");
      $publicity = <<<TEXT
        <div class=joinable background=follow color=dark-green circled has-tooltip=bottom>
          <mi>public</mi>
          <div ttooltip>
            <p text std bold>$pub_text</p>
          </div>
        </div>
      TEXT;
      break;

    case 1:
      $pub_text = __("Request to join");
      $publicity = <<<TEXT
        <div class=joinable background=besure color=dark-orange circled has-tooltip=bottom>
          <mi>arrow_circle_right</mi>
          <div ttooltip>
            <p text std bold>$pub_text</p>
          </div>
        </div>
      TEXT;
      break;

    default:
      $pub_text = __("Private");
      $publicity = <<<TEXT
        <div class=joinable background=besure color=dark-orange circled has-tooltip=bottom>
          <mi>remove</mi>
          <div ttooltip>
            <p text std bold>$pub_text</p>
          </div>
        </div>
      TEXT;
      break;
  }
}

?>

<div keeper grid-keeper flex-with-3-item>
  <a href="<?= "/squad/" . $Squad->id; ?>" sub>
    <box-model squad-card size=std clickable filled=lighter rounded=wide>
      <div headline rounded=wide>
        <picture>
          <?php $Squad->headline(); ?>
        </picture>
      </div>

      <bm-inr size=std fl gap=smol+ alic>
        <picture size=mid circled>
          <?php $Squad->logo(); ?>
        </picture>
        <div fl fldircol gap=smol style=flex:1;>
          <p text mid bold trimt><?= $Squad->name; ?></p>
          <div fl gap=smol>

            <?php switch ($CurrentUser->available_action_for($Squad)):
              case "is_member": ?>
                <div class=joinable background=green color=light-green circled has-tooltip=bottom>
                  <mi>check</mi>
                  <div ttooltip>
                    <p text std bold><?= __("Your Squad") ?></p>
                  </div>
                </div>
              <?php break;
              case "invite_pending": ?>
                <div class=joinable background=refollow color=dark-blue circled has-tooltip=bottom>
                  <p class="loader-pulse">
                    <span></span>
                  </p>
                  <div ttooltip>
                    <p text std bold><?= __("Your request is pending") ?></p>
                  </div>
                </div>
              <?php break;
              case "request_pending": ?>
                <div class=joinable background=refollow color=dark-blue circled has-tooltip=bottom>
                  <p class="loader-pulse">
                    <span></span>
                  </p>
                  <div ttooltip>
                    <p text std bold><?= __("Your request is pending") ?></p>
                  </div>
                </div>
                <?php break;
              default:
                switch ($Squad->joinable):
                  case 2: ?>
                    <div class=joinable background=follow color=dark-green circled has-tooltip=bottom>
                      <mi>globe_asia</mi>
                      <div ttooltip>
                        <p text std bold><?= __("Public"); ?></p>
                      </div>
                    </div>
                  <?php break;
                  case 1: ?>
                    <div class=joinable background=besure color=dark-orange circled has-tooltip=bottom>
                      <mi>vpn_lock</mi>
                      <div ttooltip>
                        <p text std bold><?= __("Request to join") ?></p>
                      </div>
                    </div>
                  <?php break;
                  default: ?>
                    <div class=joinable background=unfollow color=dark-red circled has-tooltip=bottom>
                      <mi>public_off</mi>
                      <div ttooltip>
                        <p text std bold><?= __("Private") ?></p>
                      </div>
                    </div>
            <?php break;
                endswitch;
            endswitch ?>

            <div fl gap=smol jucsb style=flex:1;>
              <div fl alic gap=smol filled color=dynamic rounded=wide pblock2 pinline8 style=padding-right:14px; has-tooltip=bottom>
                <i class=mi size=std>face</i>
                <p text std bold><?= $Squad->members->count(); ?></p>
                <div ttooltip>
                  <p text std bold><?= __("Members") ?></p>
                </div>
              </div>

              <?php if ($Squad->modes) : ?>
                <div fl gap=smol filled color=dynamic rounded=wide fl alic jucc gap=smol pinline10>
                  <?php

                  foreach ((array) $Squad->modes() as $active_mode => $active) :
                    if (!$active) continue;

                  ?>
                    <div has-tooltip=bottom>
                      <i text midler class="osu-icon osu-<?= $active_mode === "osu" ? "vanilla" : $active_mode; ?>"></i>
                      <div ttooltip>
                        <p text std bold><?= Gamemode::convert_mode_to_full_name($active_mode); ?></p>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </bm-inr>
    </box-model>
  </a>
</div>

<?php

/**
 * Unset possible open variables to prevent scuffed up content
 * past this one.
 */
unset($set_appart, $Squad);

?>