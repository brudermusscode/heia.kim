<?php

use Illuminate\Support\Collection;
use Heiakim\Time\Time;
use Heiakim\Model\Artist;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Gamemode;

/**
 * @var ?Beatmap\Set $Set
 * @var ?Beatmap $Beatmap
 * @var string $mode
 */

/**
 * @var Beatmap\Set
 */
$Set ??= $Beatmap?->set;

if (!$Set) : ?>
  <beatmap row ripple-effect rounded=wide clickable animation=fade-in fl alic jucc>
    <p text slight>Beatmap is missing</p>
  </beatmap>
<?php else :

  $Set->populate_artists();
  $set_last_updated = Time::ago($Set->last_osuapi_check);

  /**
   * @var Collection<Beatmap>
   */
  $Beatmaps = $Set->beatmaps()
    ->orderBy("diff", "DESC")
    ->get();

  /**
   * @var Beatmap
   */
  $FirstBeatmap = $Beatmaps->first();

  /**
   * @var Collection<Artist>
   */
  $Artists = $Set->artists;
  $more_artists = $Artists->count() > 1 ? $Artists->count() - 1 : 0;
  $is_feature = $Artists->count() > 1;

  if (in_array($FirstBeatmap->mode, Gamemode::$basic_modes))
    $mode_text = Gamemode::mode_text($FirstBeatmap->mode);
  else if (in_array($FirstBeatmap->mode, Gamemode::$modes_text))
    $mode_text = $mode;
  else
    $mode_text = "osu";

  # These variables will fetermine how to show the card in the end.
  $include_all_diffs ??= false;
  $include_time_ago ??= false;
  $include_status ??= true;

?>

  <a sub href="<?= "/beatmap-set/$Set->id/$FirstBeatmap->id/$mode_text/vanilla"; ?>">
    <beatmap row ripple-effect rounded=wide clickable animation=fade-in>

      <!--- Cover --->
      <picture cover>
        <?php $FirstBeatmap->cover(false); ?>
      </picture>

      <!--- Content --->
      <inr p28>
        <div fl alic jucsb gap=smol mb12>
          <p background=special rounded pinline8 pblock4 color=special-text
            text smol bold>
            <?php

            $duration = gmdate("H:i:s", $FirstBeatmap->total_length);

            if (preg_match("/^00:/", $duration)) {
              echo substr($duration, 3, 10);
            } else {
              echo $duration;
            }

            ?>
          </p>

          <div fl alic gap=smoler>
            <?php if ($FirstBeatmap->is_tv_size()) { ?>
              <div filled color=dynamic rounded=std has-tooltip=bottom>
                <div style="padding-inline:6px 10px;" pblock4 fl alic gap=smolest>
                  <mi smol>history_toggle_off</mi>
                  <p text smol bold ttup><?= __("TV Size") ?></p>
                </div>
                <div ttooltip>
                  <p text std bold><?= __("Short version of a longer song") ?></p>
                </div>
              </div>
            <?php } ?>

            <?php if ($is_feature) { ?>
              <div filled color=dynamic rounded=std has-tooltip=bottom>
                <div style="padding-inline:6px 10px;" pblock4 fl alic gap=smolest>
                  <mi smol>diversity_3</mi>
                  <p text smol bold ttup><?= __("Feature") ?></p>
                </div>
                <div ttooltip>
                  <p text std bold><?= __("Feature between atleast two artists") ?></p>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>

        <div fl fldircol gap=smolest>
          <p text semibold trimt style="margin-bottom:-6px;">
            <?= htmlspecialchars_decode($Artists->first()->name  ?? __("Someone")); ?>
          </p>
          <p text mid bold trimt style=line-height:1.4em;>
            <?= htmlspecialchars($FirstBeatmap->stripped_title()); ?></p>
        </div>

        <div class="beatmaps__extras" fl fldirrow jucsb alic>
          <div fl alic gap=smol>

            <?php

            $icon = match ($mode_text) {
              "taiko" => 'taiko',
              "ctb" => 'ctb',
              "mania" => 'mania',
              default => 'vanilla',
            };

            ?>

            <?php if ($include_status && !$include_time_ago) : ?>
              <div pl6 pblock6 pr12 beatmap-state="<?= $FirstBeatmap->status; ?>" rounded fl alic gap=smol>
                <i text midler class="osu-icon osu-<?= $icon; ?>"></i>
                <p text smol bold ttup>
                  <?= htmlspecialchars($FirstBeatmap->status()); ?></p>
              </div>
            <?php else : ?>
              <div rounded=wide fl alic gap=smoler background=company color=light style="padding-inline:8px 12px;" pinline4 has-tooltip=bottom>
                <mi size=smol>restore</mi>
                <p text smol><strong><?= $set_last_updated; ?></strong>
                  <?= __("ago") ?></p>

                <div ttooltip>
                  <p text bold><?= __("Last updated") ?></p>
                </div>
              </div>
            <?php endif; ?>
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
                    <p text smoler bold background=hover color=light rounded=smol pinline6 style=height:14px; fl jucc alic>and +$more</p>
                TEXT;

                  break;
                }

            ?>

                <div rounded=smoler shadowed=min <?= "beatmap-difficulty-bg=" . $diff_name; ?> has-tooltip=bottom no-trans-delay style="height:14px;width:14px;">
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
      </inr>
    </beatmap>
  </a>

<?php endif; ?>