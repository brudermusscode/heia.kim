<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/define.php";
require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/vendor/autoload.php";

use Heiakim\Model\User;
use Heiakim\Http\Request;
use Heiakim\Time\Time;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;
use Heiakim\Utils\Utils;

/**
 * @var Request $Request
 */

/**
 * @var INT
 */
$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var User
 */
$User = User::find($id);

/**
 * User doesn't exist?
 * ! Error
 */
if (!$User)
  exit($Request->error());

/**
 * Bancho status
 */
$Bancho = $User->get_bancho_game_status();

/**
 * Begin output buffer
 */
ob_start();

?>

<box-model hover-card filled data-react="users:activity" elevated animation=fade-in>
  <bm-inr size=std>
    <div fl fldircol gap=smol>
      <div fl align-items=center gap=smol>
        <picture class=country-flag>
          <img src="<?= IMAGE . "/country-flags/$user->country.svg"; ?>" loading=lazy>
        </picture>
        <div fl fldircol>
          <p text bold std><?= $User->get_name(); ?></p>
          <div fl gap=smol align-items=center>
            <?php if (!$Bancho) { ?>
              <p text smol>Last activity - <?= Time::ago(date('Y-m-d h:i:s', $user->latest_activity)); ?></p>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </bm-inr>

  <?php

  if ($Bancho) {
    $is_idle = in_array($Bancho->action_id, [0, 1]);

  ?>
    <div style=margin-top:-.8em;>
      <div pblock32 fl gap=smol align-items=center style=padding-bottom:24px;>
        <?php

        echo match ($Bancho->action_id) {
          0, 1 => "<div online-dot background=yellow></div>",
          2 => "<div pulsating online-dot background=invert></div>",
          3 => "<div pulsating online-dot background=green></div>",
          4 => "<div pulsating online-dot background=green></div>",
          6 => "<div pulsating online-dot background=blue></div>",
          default => "<div online-dot background=yellow></div>",
        };

        ?>
        <p text smol bold timestamp ttup><?= $Bancho->action; ?></p>
        <?php

        /**
         * Append mods if any activated.
         */
        if ($Bancho->action_id == 2 && $Bancho->mods) {
          $mods = Score::turn_mods_into_array($Bancho->mods);

        ?>
          <div fl gap=smol align-items=center>
            <p text smol bold timestamp ttup>-</p>
            <div fl gap=smol align-items=center>
              <?php foreach ($mods as $m) { ?>
                <p text smol bold background=slight rounded=smol pblock6 pinline2><?= $m->short; ?></p>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
      <?php if ($Bancho->beatmap_id && !$is_idle) { ?>
        <div style=padding:.2em;margin-top:-24px;>
          <?php

          $Beatmap = new Beatmap($Bancho->beatmap_id, false);
          include COMPONENT . "/beatmaps/_hover_card.php";

          ?>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</box-model>

<?php

/**
 * @var array
 */
$return_state = [
  "action" => $Bancho->action ?? "Idle",
  "beatmap_id" => $Bancho->beatmap_id ?? 0,
  "id" => Utils::create_unique_token(12),
];

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
