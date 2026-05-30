<?php

use Bruder\Heiakim\Model\Gamemode;
use Bruder\Time\Time;

/**
 * @var User $CurrentUser
 * @var Beatmap $Beatmap
 */

/**
 * Beatmap
 */
$beatmap_status_text = $Beatmap->turn_status_to_text();
$beatmap_id = $Beatmap->id;
$beatmap_set_id = $Beatmap->set_id;
$beatmap_mode_text = Gamemode::get_mode_as_text($Beatmap->mode);

/**
 * Create artist references
 */
$Beatmap->create_featured_artists();

/**
 * Artists
 */
$Artists = $Beatmap->set->artists;

/**
 * Different views
 */
$show_play_count ??= false;
$beatmap_play_count ??= 0;
$include_status ??= true;
$include_time_ago ??= false;

/**
 * @var string
 */
$last_updated = Time::ago($Beatmap->last_update, true);

?>

<a sub href='<?= "/beatmap-set/$beatmap_set_id/$beatmap_id/$beatmap_mode_text/vanilla"; ?>'>
  <box-model ripple-effect rounded=wide clickable animation="fade-in" beatmap-card>
    <div class="beatmap">
      <div class="beatmap__image">
        <picture>
          <?php include TEMPLATE . "/helper/beatmaps/_cover.php"; ?>
        </picture>
      </div>
    </div>

    <bm-inr size="mid">
      <div style="position:absolute;top:.6em;right:.6em;" fl gap=smol alic>
        <?php if ($Beatmap->is_tv_size()) { ?>
          <div background=dynamic rounded=std has-tooltip=bottom>
            <div style=padding-inline:6px+10px; pblock4 fl align-items=center gap=smol>
              <p>
                <i class="mi" size=smol>history_toggle_off</i>
              </p>
              <p text smol bold ttup><?= __("TV Size") ?></p>
            </div>
            <div ttooltip>
              <p text std bold><?= __("Short version of a longer song") ?></p>
            </div>
          </div>
        <?php } ?>

        <?php if ($Artists->count() > 1) { ?>
          <div background=dynamic rounded=std has-tooltip=bottom>
            <div style=padding-inline:6px+10px; pblock4 fl align-items=center gap=smol>
              <p>
                <i class="mi" size=smol>diversity_3</i>
              </p>
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
          <p text mid bold trimt style=line-height:1.4em;><?= htmlentities($Beatmap->title); ?></p>
        </div>
        <div fl gap=smol align-items=center>
          <p text smol bold class=duration>
            <?php

            $duration = gmdate("H:i:s", $Beatmap->total_length);

            if (preg_match("/^00:/", $duration)) {
              echo substr($duration, 3, 10);
            } else {
              echo $duration;
            }

            ?>
          </p>
          <p text std trimt><?= htmlspecialchars_decode($Artists->first()->name ?? "Someone"); ?></p>
        </div>
      </div>

      <div class="beatmaps__extras" fl fldirrow justify-content="space-between" align-items="center">
        <div fl alic gap=smol>

          <?php

          /**
           * Icon to display
           */
          $icon = match ($beatmap_mode_text) {
            "taiko" => 'taiko',
            "ctb" => 'ctb',
            "mania" => 'mania',
            default => 'vanilla',
          };

          ?>

          <?php if ($include_status && !$include_time_ago) { ?>
            <mbutton material has-icon=left no-hover beatmap-state="<?= $Beatmap->status; ?>">
              <i text midler class="osu-icon osu-<?= $icon; ?>"></i>
              <p text smol bold ttup><?= htmlspecialchars($Beatmap->status()); ?></p>
            </mbutton>
          <?php } else { ?>
            <div rounded=wide fl alic gap=smoler background=company color=light style="padding-inline:8px 12px;" pblock4
              has-tooltip=bottom>
              <mi size=smol>refresh</mi>
              <p text smol bold><?= $last_updated; ?></p>

              <div ttooltip>
                <p text bold><?= __("Last updated") ?></p>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="beatmaps__extras_diffs" fl align-items="center">
          <?php

          /**
           * Include play count either for a current user being
           * viewed in profile form or overall plays for the beatmap.
           */
          if (isset($User))
            $beatmap_times_played = $User->scores()->whereHas("beatmap", function ($q) use ($Beatmap) {
              $q->where("map_md5", $Beatmap->md5);
            })->count();
          else
            $beatmap_times_played = $Beatmap->plays;

          ?>

          <div style="padding-inline:8px 16px;" pblock8 rounded=wide fl alic gap=smol filled=darker>
            <mi>play_circle</mi>
            <p text bold><?= number_format($beatmap_times_played, 0, ".", ",") ?></p>
          </div>
        </div>
      </div>
    </bm-inr>
  </box-model>
</a>