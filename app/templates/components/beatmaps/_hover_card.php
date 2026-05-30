<?php

use Bruder\Heiakim\Model\Gamemode;

$beatmap = $Beatmap->data();

$beatmap_set_id = $beatmap->set_id;
$beatmap_difficulty = $Beatmap->get_difficulty();
$current_beatmap_mode = Gamemode::get_mode_as_text($beatmap->mode);

?>

<a href="<?= "/beatmap-set/$beatmap->set_id/$beatmap->id/$current_beatmap_mode"; ?>">
  <div class=beatmap size=smol clickable>
    <div class=bm__inr>
      <div flex-truncate>
        <p text std bold trimt style=line-height:1.32;><?= $beatmap->title; ?></p>
        <div fl align-items=center gap=smol>
          <div <?= "beatmap-difficulty-color=$beatmap_difficulty"; ?>>
            <p text std fl align-items=center>
              <?php

              echo match ($current_beatmap_mode) {
                'osu' =>   '<i class="osu-icon osu-vanilla"></i>',
                'taiko' => '<i class="osu-icon osu-taiko"></i>',
                'ctb' =>   '<i class="osu-icon osu-ctb"></i>',
                'mania' => '<i class="osu-icon osu-mania"></i>',
              };

              ?>
            </p>
          </div>
          <p text std><?= $beatmap->version; ?></p>
        </div>
      </div>
    </div>
    <div class=bm__cover>
      <picture>
        <?php

        include HELPER . "/beatmaps/_cover.php";
        unset($beatmap_set_id);

        ?>
      </picture>
    </div>
  </div>
</a>