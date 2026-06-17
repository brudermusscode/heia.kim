<?php

/**
 * @var string $base_url
 * @var string $model
 * @var string $mode
 * @var string $mod
 * @var string $link_add_country
 */

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

  <mode-menu-inline>
    <a href="<?= "$base_url/$model/osu"; ?>" sub>
      <moption ripple-effect rounded=mid <?php display_active($mode, "osu") ?>>
        <mi class="osu-icon osu-vanilla"></mi>
        <p class=text hide-mobile><?= __("Standard") ?></p>
      </moption>
    </a>
    <a href="<?= "$base_url/$model/taiko"; ?>" sub>
      <moption ripple-effect rounded=mid" <?php display_active($mode, "taiko") ?>>
        <mi class="osu-icon osu-taiko"></mi>
        <p class=text hide-mobile>Taiko</p>
      </moption>
    </a>
    <a href="<?= "$base_url/$model/ctb"; ?>" sub>
      <moption ripple-effect rounded=mid <?php display_active($mode, "ctb") ?>>
        <mi class="osu-icon osu-ctb"></mi>
        <p class=text hide-mobile>Catch</p>
      </moption>
    </a>
    <a href="<?= "$base_url/$model/mania"; ?>" sub>
      <moption ripple-effect rounded=mid <?php display_active($mode, "mania") ?>>
        <mi class="osu-icon osu-mania"></mi>
        <p class=text hide-mobile>Mania</p>
      </moption>
    </a>
  </mode-menu-inline>
</header>