<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\Gamemode;
use Heiakim\Time\Time;

/**
 * @var Beatmap\Set $Set
 */

$set_last_updated = Time::ago($Set->last_osuapi_check);

/**
 * Beatmaps
 */
$Beatmaps = $Set->beatmaps()
  ->orderBy("diff", "DESC")
  ->get();

/**
 * @var Beatmap
 */
$FirstBeatmap = $Beatmaps->first();

/**
 * Create featured artists for the beatmap.
 */
$Set->populate_artists();

/**
 * Artists
 */
$Artists = $Set->artists;
$more_artists = $Artists->count() > 1 ? $Artists->count() - 1 : 0;
$is_feature = $Artists->count() > 1;

/**
 * Mode
 */
if (in_array($FirstBeatmap->mode, Gamemode::$basic_modes))
  $mode_text = Gamemode::get_mode_as_text($FirstBeatmap->mode);
else if (in_array($FirstBeatmap->mode, Gamemode::$modes_text))
  $mode_text = $mode;
else
  $mode_text = "osu";

/**
 * Whether or not to include all difficulties to display. Some
 * sections should not display all or any.
 */
$include_all_diffs = $include_all_diffs ?? false;
$include_time_ago = $include_time_ago ?? false;
$include_status = $include_status ?? true;

?>

<a sub href="<?= "/beatmap-set/$Set->id/$FirstBeatmap->id/$mode_text/vanilla"; ?>">
  <div class="beatmap_card">
    <box-model ripple-effect rounded=wide clickable animation=fade-in beatmap-card>
      <div class="beatmap">
        <div class="beatmap__image">
          <picture>
            <?php $FirstBeatmap->cover(false); ?>
          </picture>
        </div>
      </div>

      <bm-inr size="mid">
        <div style="position:absolute;top:.6em;right:.6em;" fl gap=smol alic>
          <?php if ($FirstBeatmap->is_tv_size()) { ?>
            <div background=dynamic rounded=std has-tooltip=bottom>
              <div style="padding-inline:6px+10px;" pblock4 fl alic gap=smolest>
                <mi smol>history_toggle_off</mi>
                <p text smol bold ttup><?= __("TV Size") ?></p>
              </div>
              <div ttooltip>
                <p text std bold><?= __("Short version of a longer song") ?></p>
              </div>
            </div>
          <?php } ?>

          <?php if ($is_feature) { ?>
            <div background=dynamic rounded=std has-tooltip=bottom>
              <div style=padding-inline:6px+10px; pblock4 fl alic gap=smolest>
                <mi smol>diversity_3</mi>
                <p text smol bold ttup><?= __("Feature") ?></p>
              </div>
              <div ttooltip>
                <p text std bold><?= __("Feature between atleast two artists") ?></p>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="beatmaps__info">
          <div>
            <p text mid bold trimt style=line-height:1.4em;>
              <?= htmlspecialchars($FirstBeatmap->stripped_title()); ?></p>
          </div>
          <div fl gap=smol align-items=center>
            <p text smol bold class=duration>
              <?php

              $duration = gmdate("H:i:s", $FirstBeatmap->total_length);

              if (preg_match("/^00:/", $duration)) {
                echo substr($duration, 3, 10);
              } else {
                echo $duration;
              }

              ?>
            </p>
            <p text std trimt>
              <?= htmlspecialchars_decode($Artists->first()->name  ?? __("Someone")); ?>
            </p>
          </div>
        </div>

        <div class="beatmaps__extras" fl fldirrow jucsb alic>
          <div fl alic gap=smol>

            <?php

            /**
             * Icon to display
             */
            $icon = match ($mode_text) {
              "taiko" => 'taiko',
              "ctb" => 'ctb',
              "mania" => 'mania',
              default => 'vanilla',
            };

            ?>

            <?php if ($include_status && !$include_time_ago) { ?>
              <div class="beatmaps__extras_status" beatmap-state="<?= $FirstBeatmap->status; ?>" rounded=mid>
                <i text midler class="osu-icon osu-<?= $icon; ?>"></i>
                <p text smol bold ttup><?= htmlspecialchars($FirstBeatmap->status()); ?></p>
              </div>
            <?php } else { ?>
              <div rounded=wide fl alic gap=smoler background=company color=light style="padding-inline:8px 12px;" pinline4 has-tooltip=bottom>
                <mi size=smol>restore</mi>
                <p text smol><strong><?= $set_last_updated; ?></strong>
                  <?= __("ago") ?></p>

                <div ttooltip>
                  <p text bold><?= __("Last updated") ?></p>
                </div>
              </div>
            <?php } ?>
          </div>

          <div class="beatmaps__extras_diffs" fl alic>
            <?php

            /**
             * Only show the difficulties when not implemented on profiles. Just
             * show them on beatmaps page.
             */
            if ($include_all_diffs) {
              foreach ($Beatmaps as $key => $Beatmap) {
                $diff = $Beatmap->diff;

                $diff_name = Beatmap::difficulty_text($diff);

                if ($key == 9) {
                  $more = (int) $key - 8;

                  echo <<<TEXT
                  <div class="beatmaps__extras_diffs__diff" more fl justify-content="center" align-items="center">
                    <p>+$more</p>
                  </div>
                TEXT;

                  break;
                }

            ?>

                <div class="beatmaps__extras_diffs__diff" shadowed=min <?= "beatmap-difficulty-bg=" . $diff_name; ?> has-tooltip=bottom no-trans-delay>
                  <div class="beatmaps__extras_diffs__diff_hidden" <?= "beatmap-difficulty-bg=" . $diff_name; ?>>
                    <div ttooltip>
                      <div fl gap=smol alic>
                        <div fl gap=smolest alic>
                          <mi size=smol+ <?= "beatmap-difficulty-color=" . $diff_name; ?>>star</mi>
                          <p text std bold <?= "beatmap-difficulty-color=" . $diff_name; ?>>
                            <?= number_format($Beatmap->diff, 1); ?>
                          </p>
                        </div>
                        <p text std bold><?= htmlspecialchars($Beatmap->version); ?></p>
                      </div>
                    </div>
                  </div>
                </div>

            <?php

              }
            }

            ?>
          </div>
        </div>
      </bm-inr>
    </box-model>
  </div>
</a>