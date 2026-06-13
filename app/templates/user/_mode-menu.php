<?php if (!$is_sub_page) : ?>
  <mode-menu>
    <jump-menu mm-menu filled="lighter" elevated color="dynamic">
      <div jm-inr>
        <a href="<?= "$base_url/osu/vanilla" . ($more ? "/$more" : ""); ?>">
          <div ripple-effect class="jm__option" hoverable>
            <mi class="osu-icon osu-vanilla"></mi>
            <p text><?= __("Standard"); ?></p>
            <?php if ($mode === "osu") { ?>
              <div class=jm__option_right_icon>
                <mi color=company>done</mi>
              </div>
            <?php } ?>
          </div>
        </a>
        <a href="<?= "$base_url/taiko/vanilla" .
                    ($more ? "/$more" : ""); ?>">
          <div ripple-effect class="jm__option" hoverable>
            <mi class="osu-icon osu-taiko"></mi>
            <p text>Taiko</p>
            <?php if ($mode === "taiko") { ?>
              <div class=jm__option_right_icon>
                <mi color=company>done</mi>
              </div>
            <?php } ?>
          </div>
        </a>
        <a href="<?= "$base_url/ctb/vanilla" . ($more ? "/$more" : ""); ?>">
          <div ripple-effect class="jm__option" hoverable>
            <mi class="osu-icon osu-ctb"></mi>
            <p text>Catch the Beat</p>
            <?php if ($mode === "ctb") { ?>
              <div class=jm__option_right_icon>
                <mi color=company>done</mi>
              </div>
            <?php } ?>
          </div>
        </a>
        <a href="<?= "$base_url/mania/vanilla" .
                    ($more ? "/$more" : ""); ?>">
          <div ripple-effect class="jm__option" hoverable>
            <mi class="osu-icon osu-mania"></mi>
            <p text>Mania</p>
            <?php if ($mode === "mania") { ?>
              <div class=jm__option_right_icon>
                <mi color=company>done</mi>
              </div>
            <?php } ?>
          </div>
        </a>
      </div>
    </jump-menu>

    <mbutton mm-open size=wide has-icon=left elevated=mid material background=company color=light>
      <div mm-open-loading>
        <?php include COMPONENT . "/dot-loader.html"; ?>
      </div>
      <mi class="osu-icon osu-<?= $mode === "osu" ? "vanilla" : $mode; ?>"></mi>
      <div>
        <p text bold>
          <?= ucfirst(
            $mode === "osu"
              ? __("Standard")
              : ($mode === "ctb"
                ? "Catch the Beat"
                : $mode)
          ); ?>
        </p>
      </div>
    </mbutton>
  </mode-menu>
<?php endif; ?>