<?php

use Heiakim\Model\Artist;
use Heiakim\Time\Time;
use Heiakim\Model\Gamemode;
use Heiakim\Model\Beatmap;
use Illuminate\Support\Collection;

/**
 * @var Beatmap $Beatmap
 */

$beatmap_status_text = $Beatmap->turn_status_to_text();
$beatmap_id = $Beatmap->id;
$beatmap_set_id = $Beatmap->set_id;
$beatmap_mode_text = Gamemode::mode_text($Beatmap->mode);

$Beatmap->create_featured_artists();

/**
 * @var Collection<Artist>
 */
$Artists = $Beatmap->set->artists;

$show_play_count ??= false;
$beatmap_play_count ??= 0;
$include_status ??= true;
$include_time_ago ??= false;
$last_updated = Time::ago($Beatmap->last_update, true);

?>

<a href='<?= "/beatmap-set/$beatmap_set_id/$beatmap_id/$beatmap_mode_text/vanilla"; ?>'>
  <beatmap column clickable animation=fade-in>
    <picture>
      <?php $Beatmap->cover(); ?>
    </picture>

    <beatmap-content flex-truncate>
      <p text std trimt semibold lh1>
        <?= htmlspecialchars_decode($Artists->first()->name ?? "Someone"); ?></p>

      <p text midler bold trimt style="margin-top:-4px;margin-bottom:4px;">
        <?= htmlentities($Beatmap->title); ?></p>

      <div fl gap=smoler alic>
        <p text smol bold background=special color=special-text pinline8 pblock2 rounded>
          <?php

          $duration = gmdate("H:i:s", $Beatmap->total_length);

          if (preg_match("/^00:/", $duration)) {
            echo substr($duration, 3, 10);
          } else {
            echo $duration;
          }

          ?>
        </p>

        <p text smol semibold ttup pl4 pr8 pblock2 rounded fl alic gap=smol
          beatmap-state="<?= $Beatmap->status; ?>">
          <?php

          $icon = match ($beatmap_mode_text) {
            "taiko" => 'taiko',
            "ctb" => 'ctb',
            "mania" => 'mania',
            default => 'vanilla',
          };

          ?>
          <mi smoler class="osu-icon osu-<?= $icon; ?>"></mi>
          <?= htmlspecialchars($Beatmap->status()); ?>
        </p>
      </div>
    </beatmap-content>
  </beatmap>
</a>