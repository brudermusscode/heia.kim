<?php

/**
 * @var Artist $Artists
 */

$small ??= false;

if ($Artists->count()) {

?>

  <div fl align-items=start justify-content=space-between gap style=flex:1;>
    <div fl gap=smol align-items=center>

      <?php

      /**
       * First Artist name
       */
      $artist_name = htmlspecialchars_decode($Artists->first()->name);

      /**
       * What to show based on things for the artist element.
       */
      $show_more_artists = $Artists->count() > 1 ? "many" : "";
      $show_artist_class = $Artists->count() > 0 ? "class=artists_dropdown" : "class=artists_dropdown-no-bg";

      /**
       * Create a link to the artist of the current beatmap,
       * if there is no featured artist here.
       */
      if ($Artists->count() == 1)
        echo "<a href=\"/artist/" . $Artists->first()->id . "\">";

      ?>

      <div <?= $show_artist_class . " " . $show_more_artists; ?> fl align-items=center style=gap:.4em;>
        <p text <?= $small ? "std" : "midler"; ?> bold trimt style="line-height:inherit;">
          <?= $artist_name; ?>
        </p>

        <?php

        /**
         * Show the `& more` dialogue with the hover
         * dropdown, if there is more than one artist
         * featured in this beatmap.
         */
        if ($Artists->count() > 1) {

        ?>
          <div class=artist__more fl align-items=center gap=smol>
            <p color=yellow text <?= $small ? "std" : "midler"; ?> style=white-space:nowrap;>&
              <?= $Artists->count() - 1; ?> <?= __("others") ?>
            </p>
            <p>
              <i class="mi">keyboard_arrow_down</i>
            </p>

            <div class="hover_menu">
              <div pinline38 mb=smol>
                <p text bold smol ttup style=opacity:.6;><?= __("All artists") ?></p>
              </div>
              <?php foreach ($Artists as $Artist) { ?>
                <a href="<?= "/artist/$Artist->id"; ?>">
                  <div class=hover_menu__option>
                    <p text std><?= $Artist->name; ?></p>
                  </div>
                </a>
              <?php } ?>
            </div>
          </div>
        <?php } ?>

      </div>

      <?php

      /**
       * One artist only, close the `a` tag again.
       */
      if ($Artists->count() == 1)
        echo "</a>";

      ?>

    </div>
  </div>

<?php } else { ?>

  <div fl align-items=start justify-content=space-between gap style=flex:1;>
    <div fl gap=smol align-items=center>
      <div class=artists_dropdown-no-bg fl align-items=center style=gap:.4em;>
        <p text midler bold trimt style="line-height:inherit;">
          <?= __("Unknown Artist") ?>
        </p>
      </div>
    </div>
  </div>

<?php } ?>