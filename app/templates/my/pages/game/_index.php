<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\User;
use Heiakim\Time\Time;


$gm = Gamemode::get_gumode_as_text(CurrentUser->preferred_mode);

?>

<div fl fldircol gap=smol+>
  <?php if (FROZEN || CurrentUser->is_restricted()) { ?>
    <div fl fldircol gap>
      <a href="<?= "/my/game/restriction"; ?>" sub>
        <box-model style="background:#C9534F;" color=white elevated clickable>
          <bm-inr size=mid>
            <div p6 fl gap align-items=center justify-content=space-between>
              <div fl gap alic>
                <mi wide>raven</mi>
                <div>
                  <p text std bold>Restriction</p>
                  <p text std>Reason & progress of your restriction</p>
                </div>
              </div>

              <mi midler>east</mi>
            </div>
          </bm-inr>
        </box-model>
      </a>
    </div>
  <?php } ?>

  <box-model outlined=darker p24>
    <div p12>
      <p text midler bold><?= __("Favorite mode") ?></p>
      <p text std>
        <?= __("What you play the most. Other players will see you in specific categories of this gamemode.") ?>
      </p>
    </div>

    <div hoverable p12 rounded=mid
      data-category=<?= $category ?>
      data-sub=mode>
      <div fl gap align-items=center justify-content=space-between>
        <div fl gap align-items=center>
          <p text wide>
            <?php

            echo match ($gm->mode) {
              "osu" => "<i class=\"osu-icon osu-vanilla\"></i>",
              "taiko" => "<i class=\"osu-icon osu-taiko\"></i>",
              "mania" => "<i class=\"osu-icon osu-mania\"></i>",
              "ctb" => "<i class=\"osu-icon osu-ctb\"></i>",
            };

            ?>
          </p>
          <p text std>
            <?= ($gm->mode == "osu" ? "osu!" : ($gm->mode == "ctb" ? "Catch the Beat" : ucfirst($gm->mode)
            )) . ", " . ucfirst($gm->mod); ?>
          </p>
        </div>
        <mi midler>east</mi>
      </div>
    </div>
  </box-model>

  <box-model outlined=darker p24>
    <div p12>
      <p text midler bold>Scores</p>
      <p text std>
        <?= __("All your scores managable at one place. Scores are public to any other player.") ?>
      </p>
    </div>
    <div hoverable p12 rounded=mid
      data-category=<?= $category ?>
      data-sub=scores>
      <div fl gap align-items=center justify-content=space-between>
        <div fl gap align-items=center>
          <mi wide>trending_up</mi>
          <p text std><?= __("Overview") ?></p>
        </div>
        <mi midler>east</mi>
      </div>
    </div>
  </box-model>
</div>

<div fl fldircol gap>
  <div title-inline>
    <p text mid bold><?= __("Careful section") ?></p>
    <p text std><?= __("Functionality to clear out your gameplay") ?></p>
  </div>

  <box-model filled p24>
    <div hoverable p12 style="padding-right:32px;" rounded=mid
      data-category=<?= $category ?>
      data-sub=restart>
      <div fl gap align-items=center justify-content=space-between>
        <div fl gap align-items=center>
          <div style="width:3.2em;" fl justify-content=center align-items=center>
            <mi mid>restart_alt</mi>
          </div>
          <div>
            <p text std bold><?= __("Restart journey") ?></p>
            <p text std>
              <?php

              echo CurrentUser->settings->account_wiped_at
                ? "Last restart &middot; <span color=company>" . Time::ago(CurrentUser->settings->account_wiped_at, true) . "</span>"
                : __("Wipe all your scores and start fresh");

              ?>
            </p>
          </div>
        </div>

        <mi midler>east</mi>
      </div>
    </div>
  </box-model>
</div>