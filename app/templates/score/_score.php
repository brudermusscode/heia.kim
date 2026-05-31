<?php

use Heiakim\Time\Time;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Score;

/**
 * @var Score $Score
 */

/**
 * @var Beatmap
 */
$Beatmap = $Score?->beatmap;

if (!$Beatmap || !$Score) {

?>

  <div keeper grid-keeper>
    <box-model score rounded=mid class=score_card animation=fade-in fl fldircol justify-content=center>
      <div fl fldircol alic jucc gap=smol>
        <mi size=wide>skull</mi>
        <p text>Beatmap is missing</p>
      </div>
    </box-model>
  </div>

  <style>
    .rounder-looper-ass {
      display: block;
      rotate: 0;
      animation: rounder-looper-ass 2s ease-in-out infinite;
    }

    @keyframes rounder-looper-ass {
      from {
        rotate: 0deg;
        scale: 1;
      }

      25% {
        scale: 1.12;
      }

      50% {
        rotate: 360deg;
        scale: 1;
      }

      to {
        rotate: 360deg;
      }
    }
  </style>

<?php

} else {

  /**
   * @var string
   */
  $timestamp = Time::ago($Score->play_time);

  /**
   * @var User
   */
  $ScoreUser = $Score->user;
  $is_my_score = $ScoreUser->id == CurrentUser->id;

  /**
   * Create artists references.
   */
  $Beatmap->create_featured_artists();

  /**
   * @var Artist
   */
  $Artists = $Beatmap->set->artists;

  /**
   * Else
   */
  $clean_appearance ??= null;
  $hide_reactions ??= false;
  $cleanup_variables ??= true;

?>

  <div keeper grid-keeper fl fldircol gap=smol>
    <box-model score rounded=wide class=score_card data-id="<?= $Score->id; ?>" animation="fade-in">

      <?php

      if (!$clean_appearance)
        include __DIR__ . "/_dropdown.php";

      ?>

      <div class=score_card__map_cover>
        <picture>
          <?php $Beatmap->set->cover(); ?>
        </picture>
      </div>

      <div class=ass_outer>
        <div z class="score_card__info" background>
          <div fl jucsb gap mb>
            <div flex-truncate fl fldircol gap=smoler>
              <div fl gap alic jucsb>
                <div fl gap=smol alic>
                  <div fl gap=smol align-items=center pblock6 pinline12 rounded=wide
                    score-grade=<?= strtolower($Score->grade); ?>>
                    <div fl gap=smol pinline4>
                      <p text smol bold color=white><?= $Score->grade(); ?></p>
                    </div>
                  </div>
                  <p text mid bold><?= number_format($Score->pp); ?></p>
                  <div fl gap=smol alic pblock6 pinline12 rounded=wide background=slight show-hover>
                    <p text smol bold trimt><?= number_format($Score->acc, 2); ?> %</p>
                  </div>
                </div>

                <div fl gap=smol alic>
                  <?php if ($Score->mods()) { ?>
                    <div fl gap=smoler alic pblock6 pinline12 rounded=wide background=slight>
                      <?php

                      foreach ($Score->mods() as $key => $score_mod) {
                        if (strtolower($score_mod->short) === 'nc')
                          continue;

                        /**
                         * Echo out the mod inside a paragraph.
                         */
                        echo <<<TEXT
                          <div has-tooltip=bottom cursor=help>
                            <p text smol bold>$score_mod->short</p>
                            <div ttooltip>
                              <p text bold>$score_mod->full</p>
                            </div>
                          </div>
                        TEXT;

                        /**
                         * Unset not needed variables to prevent a
                         * scuffed up page afterwards.
                         */
                        unset($score_mod);
                      }

                      ?>
                    </div>
                    <p text smol color=company>&middot;</p>
                  <?php } ?>

                  <div has-tooltip=bottom>
                    <p text smol color=company trimt><?= Time::ago($Score->play_time, true); ?></p>
                    <div ttooltip>
                      <p text bold><?= __("Play time") ?></p>
                    </div>
                  </div>
                </div>
              </div>
              <div fl gap=smol>
                <p text std trimt><strong><?= $Beatmap->stripped_title(); ?></strong></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="overlow_hidden_on_hover_out">
        <div class=score_card__info_sub fl jucstart flex-truncate>

          <div fl gap=smol alic style="padding-inline:12px 18px;padding-block:6px;" rounded
            beatmap-difficulty-bg="<?= $Beatmap->turn_difficulty_to_text(); ?>" color=white flex-truncate>
            <div fl alic style=gap:2px;>
              <mi size=smol style=margin-top:-2px;>star</mi>
              <p text smol bold><?= number_format($Beatmap->diff, 2); ?></p>
            </div>
            <p text smol trimt><?= $Beatmap->version; ?></p>
          </div>

          <?php if (!$clean_appearance) { ?>
            <div data-action=" beatmap:set,play" data-set-id="<?= $Beatmap->set_id; ?>" active="false" dno>
              <mbutton material filled=lighter icon-only>
                <mi>play_arrow</mi>
              </mbutton>
            </div>
          <?php } ?>
        </div>
      </div>
    </box-model>

    <?php

    if (LOGGED && !$hide_reactions && !$clean_appearance) {
      $has_reactions = $Score->reactions->count();
      $disable_reactions_view = $has_reactions ?: "style=display:none;"; ?>
      <div reactions-outer>
        <form data-form="reactions:create">
          <input type=hidden name=reference_id value=<?= $Score->id; ?> />
          <input type=hidden name=type value=score />
          <input type=hidden name=reaction />
        </form>

        <div reactions-container fl gap=smoler data-action="reactions:create" data-id=<?= $Score->id; ?>>
          <?php

          $Reactions = $Score->reactions()
            ->groupBy("reaction")
            ->get();

          foreach ($Reactions ?? [] as $Reaction)
            include TEMPLATE . "/reaction/_reaction.php";

          ?>
        </div>
      </div>
    <?php } ?>
  </div>

<?php

  if ($cleanup_variables)
    unset($Beatmap, $ScoreUser, $Artists, $Score, $clean_appearance, $hide_reactions, $is_my_score, $timestamp);
}

?>