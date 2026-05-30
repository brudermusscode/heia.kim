<?php

use Bruder\Heiakim\Model\Leaderboard;

/**
 * Countries
 */
$countries = Leaderboard::get_cached_countries($gumode);

?>

<div class="countries">
  <div class=lb__countries>
    <div mb pblock12>
      <p text bold std>Country</p>
    </div>

    <div fl style=gap:.6em; align-items=center flex-wrap=wrap>
      <a sub href="<?= "/leaderboard/$mode/$mod/$type/global"; ?>">
        <div <?php if ($country == "global") echo "active"; ?> class=option background=slight fl justify-content=center
          align-items=center has-tooltip=bottom clickable>
          <p text mid>
            <i class="mi">public</i>
          </p>
          <div ttooltip>
            <p text std bold>Global</p>
          </div>
        </div>
      </a>

      <?php

      foreach ($countries as $c) {

        if (!$c || $c == "xx") continue;

        $c_country = $c;
        $country_name = Locale::getDisplayRegion("-" . strtoupper($c_country), 'en');
        $filepath = IMAGE . '/country-flags/' . $c_country . ".svg";
        $active = $country === $c ? 'active' : '';

      ?>

        <a sub href="<?= "/leaderboard/$mode/$mod/$type/$c_country"; ?>">
          <picture class=option <?= $active; ?> has-tooltip=bottom clickable>
            <img src="<?= $filepath; ?>" loading=lazy />
            <div ttooltip>
              <p text std bold><?= $country_name; ?></p>
            </div>
          </picture>
        </a>

      <?php

      }

      ?>
    </div>
  </div>
</div>