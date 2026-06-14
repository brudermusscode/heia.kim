<?php

use Heiakim\Model\User\UserSettingsPremium;

/**
 * @var array
 */
$benefits = UserSettingsPremium::$benefits_display;

?>

<div expand-more fl fldircol gap=smoler>

  <?php for ($i = 0; $i < 4; $i++) { ?>
    <div outlined=darker rounded p14>
      <div fl alic gap=smol+>
        <mi color=green><?= isset($benefits[$i]["icon"]) ? $benefits[$i]["icon"] : "task_alt"; ?></mi>
        <p text bold><?= $benefits[$i]["text"]; ?></p>
      </div>
    </div>
  <?php unset($benefits[$i]);
  } ?>

  <div expand-more-hidden fl fldircol gap=smoler>
    <?php foreach ($benefits as $benefit) { ?>
      <div outlined=darker rounded p14>
        <div fl alic gap=smol+>
          <div <?= isset($benefit["tooltip"]) ? "has-tooltip=bottom" : ""; ?>>

            <?php if (isset($benefit["tooltip_link"])) { ?>
              <a href="<?= $benefit["tooltip_link"]; ?>">
              <?php } ?>
              <mi color=<?= isset($benefit["color"]) ? $benefit["color"] : "green"; ?>>
                <?= isset($benefit["icon"]) ? $benefit["icon"] : "task_alt"; ?>
              </mi>
              <?php if (isset($benefit["tooltip_link"])) { ?>
              </a>
            <?php } ?>

            <?php if (isset($benefit["tooltip"])) { ?>
              <div ttooltip>
                <p text bold><?= $benefit["tooltip_text"] ?? "N/A"; ?></p>
              </div>
            <?php } ?>
          </div>
          <p text bold><?= $benefit["text"]; ?></p>
        </div>
      </div>
    <?php } ?>
  </div>

  <div fl jucc mt=smol>
    <mbutton smol expand-more-show filled has-icon=right>
      <p text expand-more-text></p>
      <mi>unfold_more</mi>
    </mbutton>
  </div>
</div>