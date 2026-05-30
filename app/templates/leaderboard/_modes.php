<div class="modes" fl fldirrow justify-content="center" gap>
  <a sub href="<?= "/leaderboard/osu/vanilla" . $link_add_type . $link_add_country; ?>">
    <div ripple-effect class="modes__mode" <?= $mode === 'osu' ? "active='true'" : ''; ?> has-tooltip=bottom
      smol>
      <p><i class="osu-icon osu-vanilla"></i></p>
      <div tooltip>
        <div class="tooltip__outer">
          <div class="tooltip__outer_text" text-only fl gap="smol">
            <p trimt><?= __("Standard") ?></p>
          </div>
        </div>
      </div>
    </div>
  </a>

  <a sub href="<?= "/leaderboard/taiko/vanilla" . $link_add_type . $link_add_country; ?>">
    <div ripple-effect class="modes__mode" <?= $mode === 'taiko' ? "active='true'" : ''; ?> has-tooltip=bottom
      smol>
      <p><i class="osu-icon osu-taiko"></i></p>
      <div tooltip>
        <div class="tooltip__outer">
          <div class="tooltip__outer_text" text-only fl gap="smol">
            <p trimt>Taiko</p>
          </div>
        </div>
      </div>
    </div>
  </a>

  <a sub href="<?= "/leaderboard/ctb/vanilla" . $link_add_type . $link_add_country; ?>">
    <div ripple-effect class="modes__mode" <?= $mode === 'ctb' ? "active='true'" : ''; ?> has-tooltip=bottom
      smol>
      <p><i class="osu-icon osu-ctb"></i></p>
      <div tooltip>
        <div class="tooltip__outer">
          <div class="tooltip__outer_text" text-only fl gap="smol">
            <p trimt>Catch the Beat</p>
          </div>
        </div>
      </div>
    </div>
  </a>

  <a sub href="<?= "/leaderboard/mania/vanilla" . $link_add_type . $link_add_country; ?>">
    <div ripple-effect class="modes__mode" <?= $mode === 'mania' ? "active='true'" : ''; ?> has-tooltip=bottom
      smol>
      <p><i class="osu-icon osu-mania"></i></p>
      <div tooltip>
        <div class="tooltip__outer">
          <div class="tooltip__outer_text" text-only fl gap="smol">
            <p trimt>Mania</p>
          </div>
        </div>
      </div>
    </div>
  </a>
</div>

<select-model slight alignment="top">
  <div ripple-effect data-action="element:select" class="select_model__inr" rounded=std>
    <div class="select_model__inr_optionshown">
      <div fl gap=std align-items=center>
        <p select-model-value class="text">
          <?php

          switch ($mod) {
            case 'vanilla':
              echo 'Vanilla';
              break;
            case 'relax':
              echo 'Relax';
              break;
            case 'autopilot':
              echo 'Autopilot';
              break;
          }

          ?>
        </p>
      </div>
      <p class="select_model__inr_optionshown__dropicon" no-bg>
        <i class="ri-arrow-down-s-line"></i>
      </p>
    </div>
  </div>

  <div select-model-dropdown class="select_model__dropdown" shadowed=min rounded=std>
    <div get-height>
      <a sub href="<?= "/leaderboard/$mode/vanilla" . $link_add_type . $link_add_country; ?>">
        <div ripple-effect class="select_model__dropdown_option">
          <p select-model-dropdown-option text <?= $mod === 'vanilla' ? 'bold' : ''; ?>>Vanilla
          </p>
        </div>
      </a>

      <?php if (in_array($mode, ['osu', 'taiko', 'ctb'])) { ?>
        <a sub href="<?= "/leaderboard/$mode/relax" . $link_add_type . $link_add_country; ?>">
          <div ripple-effect class="select_model__dropdown_option">
            <p select-model-dropdown-option text <?= $mod === 'relax' ? 'bold' : ''; ?>>Relax
            </p>
          </div>
        </a>
      <?php } ?>

      <?php if (in_array($mode, ['osu'])) { ?>
        <a sub href="<?= "/leaderboard/$mode/autopilot" . $link_add_type . $link_add_country; ?>">
          <div ripple-effect class="select_model__dropdown_option">
            <p select-model-dropdown-option text <?= $mod === 'autopilot' ? 'bold' : ''; ?>>
              Autopilot</p>
          </div>
        </a>
      <?php } ?>
    </div>
  </div>
</select-model>