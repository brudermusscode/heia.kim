<?php

use Heiakim\Model\Gamemode;
use Heiakim\Time\Time;

$gm = Gamemode::gumode_text(CurrentUser->preferred_mode);

?>

<div fl fldircol gap=smol+>
  <?php if (FROZEN || CurrentUser->is_restricted()) { ?>
    <box-model open="game:restriction" style="background:#C9534F;" color=white clickable p24 mb12>
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
    </box-model>
  <?php } ?>

  <box-model outlined=darker p24>
    <div p12 fl fldircol gap=smol>
      <p text midler bold><?= __("Favorite mode") ?></p>
      <p text std>
        <?= __("What you play the most. Other players will see you in specific categories of this gamemode.") ?>
      </p>
    </div>

    <div open="game:mode" hoverable p12 rounded=mid fl gap alic jucsb>
      <div fl gap alic>
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
        <p text std color=company bold>
          <?= ($gm->mode == "osu" ? "osu!" : ($gm->mode == "ctb" ? "Catch the Beat" : ucfirst($gm->mode)
          )) . ", " . ucfirst($gm->mod); ?>
        </p>
      </div>
      <mi midler>east</mi>
    </div>
  </box-model>

  <box-model outlined=darker p24>
    <div p12 fl fldircol gap=smol>
      <p text midler bold>Scores</p>
      <p text std>
        <?= __("All your scores managable at one place. Scores are public to any other player.") ?>
      </p>
    </div>
    <div open="game:scores" hoverable p12 rounded=mid>
      <div fl gap alic jucsb>
        <div fl gap alic>
          <mi wide>trending_up</mi>
          <p text color=company bold><?= __("Overview") ?></p>
        </div>
        <mi midler>east</mi>
      </div>
    </div>
  </box-model>
</div>

<div fl fldircol gap=smol+>
  <p text mid bold title-inline><?= __("Others") ?></p>

  <box-model filled p24>
    <div open="game:restart" hoverable p12 style="padding-right:32px;" rounded=mid>
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