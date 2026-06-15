<?php

use Heiakim\Http\Request;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Score;
use Illuminate\Support\Collection;

$limit = 4;
$status = 2;
$score_status = 2;
$order = 'pp';
$sort = 'DESC';
$limit = 18;

$mode   = Request::sanitize_get_param("mode", "osu");
$mod    = Request::sanitize_get_param("mod", "vanilla");
$base_url = "/my/game/scores";
$gumode = Gamemode::find_gumode($mode, $mod, array: false);

/**
 * @var Collection<Score>
 */
$Scores = CurrentUser->scores()
  ->where("mode", $gumode)
  ->orderByDesc("pp")
  ->orderByDesc("status")
  ->limit(20)
  ->get();

?>

<div fl gap=mid alic>
  <?php include TEMPLATE . "/my/_back_button.php"; ?>

  <label size="mid" has-secondary>
    <div class="label__main">
      <p bold>Scores</p>
    </div>
  </label>
</div>

<div fl jucc style="max-width: 620px; margin-inline: auto">
  <div background="dynamic" pblock32 pinline32 rounded="wide">
    <div fl gap>
      <p text mid normalize-icon>
        <i class="mi">tips_and_updates</i>
      </p>
      <p text std>
        This overview is deprecated. A dedicated score manager will soon be implemented.
      </p>
    </div>
  </div>
</div>

<div fl gap=smol style=z-index:1002;position:relative; disabled>
  <a sub href="<?= "$base_url/osu/$mod"; ?>">
    <div has-tooltip=bottom>
      <mbutton mid <?= $mode == "osu" ? "active" : "background=slighter"; ?> icon-only ripple-effect>
        <i class="osu-icon osu-vanilla"></i>
      </mbutton>

      <div ttooltip>
        <p text std bold><?= __("Standard") ?></p>
      </div>
    </div>
  </a>

  <a sub href="<?= "$base_url/taiko/vanilla"; ?>">
    <div has-tooltip=bottom>
      <mbutton mid <?= $mode == "taiko" ? "active" : "background=slighter"; ?> icon-only ripple-effect>
        <i class="osu-icon osu-taiko"></i>
      </mbutton>

      <div ttooltip>
        <p text std bold>Taiko</p>
      </div>
    </div>
  </a>

  <a sub href="<?= "$base_url/ctb/vanilla"; ?>">
    <div has-tooltip=bottom>
      <mbutton mid <?= $mode == "ctb" ? "active" : "background=slighter"; ?> icon-only ripple-effect>
        <i class="osu-icon osu-ctb"></i>
      </mbutton>

      <div ttooltip>
        <p text std bold>Catch the Beat</p>
      </div>
    </div>
  </a>

  <a sub href="<?= "$base_url/mania/vanilla"; ?>">
    <div has-tooltip=bottom>
      <mbutton mid <?= $mode == "mania" ? "active" : "background=slighter"; ?> icon-only ripple-effect>
        <i class="osu-icon osu-mania"></i>
      </mbutton>

      <div ttooltip>
        <p text std bold>Mania</p>
      </div>
    </div>
  </a>

  <mselect size=mid outlined=darker align=center clickable mselect-type=visible>
    <div class=mselect__inr fl alic gap=smol>
      <p mselect-visible-value text bold><?= ucfirst($mod); ?></p>
      <i class="mi" size=smol>expand_all</i>
    </div>
    <mselect-dropdown>
      <div get-size>
        <div class=msd__inr>
          <a sub href="<?= "$base_url/$mode/vanilla"; ?>">
            <mselect-option mselect-input-value mselect-change-visible-value>
              <p>Vanilla</p>
            </mselect-option>
          </a>

          <?php if (in_array($mode, ["osu", "taiko", "ctb"])) { ?>
            <a sub href="<?= "$base_url/$mode/relax"; ?>">
              <mselect-option mselect-input-value mselect-change-visible-value>
                <p>Relax</p>
              </mselect-option>
            </a>
          <?php } ?>

          <?php if (in_array($mode, ["osu"])) { ?>
            <a sub href="<?= "$base_url/$mode/autopilot"; ?>">
              <mselect-option mselect-input-value mselect-change-visible-value>
                <p>Autopilot</p>
              </mselect-option>
            </a>
          <?php } ?>
        </div>
      </div>
    </mselect-dropdown>
  </mselect>
</div>

<div grid-repeat gap=smol>
  <?php

  if (!$Scores->count())
    echo <<<TEXT
    <div class="content_placeholder">
      <div class="content_placeholder_inr">
        <p text wide style=opacity:.6;><i class="ri-medal-fill"></i></p>
        <p text bold smol style=opacity:.6; ttup>No scores set</p>
      </div>
    </div>
    TEXT;
  else {
    $clean_appearance = true;

    foreach ($Scores as $Score)
      include TEMPLATE . "/score/_score.php";
  }

  ?>
</div>