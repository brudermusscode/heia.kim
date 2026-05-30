<?php

/**
 * Include main page navigator.
 */
include PAGE_NAVIGATOR;

?>

<header page scroll-manipulated>
  <div class=title>
    <div fl align-items=center fldircol>
      <h1 text wide bold title><?= __("Rankings") ?></h1>
      <section>
        <p text smol tac><?= __("Rankings of all modes, updated weekly") ?></p>
      </section>
    </div>
  </div>

  <div posrel>
    <div class="outer">
      <a href="<?= "$base_url/osu/vanilla/$type/$country"; ?>" sub>
        <div ripple-effect rounded=mid class="option" <?php if ($mode === "osu") echo "active"; ?>>
          <p class=icon>
            <i class="osu-icon osu-vanilla"></i>
          </p>
          <p class=text hide-700><?= __("Standard") ?></p>
        </div>
      </a>
      <a href="<?= "$base_url/taiko/vanilla/$type/$country"; ?>" sub>
        <div ripple-effect rounded=mid class="option" <?php if ($mode === "taiko") echo "active"; ?>>
          <p class=icon>
            <i class="osu-icon osu-taiko"></i>
          </p>
          <p class=text hide-700>Taiko</p>
        </div>
      </a>
      <a href="<?= "$base_url/ctb/vanilla/$type/$country"; ?>" sub>
        <div ripple-effect rounded=mid class="option" <?php if ($mode === "ctb") echo "active"; ?>>
          <p class=icon>
            <i class="osu-icon osu-ctb"></i>
          </p>
          <p class=text hide-700>Catch</p>
        </div>
      </a>
      <a href="<?= "$base_url/mania/vanilla/$type/$country"; ?>" sub>
        <div ripple-effect rounded=mid class="option" <?php if ($mode === "mania") echo "active"; ?>>
          <p class=icon>
            <i class="osu-icon osu-mania"></i>
          </p>
          <p class=text hide-700>Mania</p>
        </div>
      </a>
    </div>
  </div>
</header>