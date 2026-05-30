<div class="mobile_menu">
  <div class="mobile_menu__inr">
    <a href="/home">
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="ri-arrow-left-line"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Back</p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "$base_url/osu/$current_mod"; ?>" sub <?php if ($mode === "osu") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-vanilla"></i></p>
          </div>
          <div class=text>
            <p text smol ttup><?= __("Standard") ?></p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "$base_url/taiko/$current_mod"; ?>" sub <?php if ($mode === "taiko") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-taiko"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Taiko</p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "$base_url/ctb/$current_mod"; ?>" sub <?php if ($mode === "ctb") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-ctb"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>CTB</p>
          </div>
        </div>
      </div>
    </a>

    <a href="<?= "$base_url/mania/$current_mod"; ?>" sub <?php if ($mode === "mania") echo "active"; ?>>
      <div class="mobile_menu__inr_option">
        <div alitcent flcol>
          <div class="icon" pinline2>
            <p><i class="osu-icon osu-mania"></i></p>
          </div>
          <div class=text>
            <p text smol ttup>Mania</p>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>