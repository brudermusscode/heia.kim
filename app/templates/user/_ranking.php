<?php

use Bruder\Heiakim\Model\User;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var int $gumode
 * @var bool $is_champion
 */

?>

<?php if (!$has_played) { ?>

  <mbutton material has-icon=left has-icon=right size=mid tag filled>
    <mi>panorama_fish_eye</mi>
    <p text bold smol ttup trimt>Never played</p>

    <div dot-divider></div>

    <div fl alic gap=smol>
      <?php if ($User->country == "xx") { ?>
        <mi>circle</mi>
      <?php } else { ?>
        <picture size=smol circled>
          <?php $User->country()->first()->icon(); ?>
        </picture>
      <?php } ?>
      <p text smol bold ttup><?= $User->country_string(); ?></p>
    </div>
  </mbutton>

<?php } else { ?>

  <div fl alic gap=smol+ z>
    <div fl alic gap=smol has-tooltip=bottom z curwhat>
      <!-- <mi midler><?= METRIC_ICON; ?></mi> -->
      <p text mid><?= number_format($Stats->pp); ?> pp</p>
      <div ttooltip>
        <p text bold>Performance Points</p>
      </div>
    </div>


    <?php if ($is_champion) { ?>


      <mbutton material has-icon=left has-icon=right size=mid tag background=special color=light has-tooltip=bottom curwhat>
        <mi mid>globe</mi>
        <p text bold smol ttup>Champion</p>
        <div ttooltip>
          <p text>
            <strong>#1 Global</strong> - <?= $mode . " " . $current_mod; ?>
          </p>
        </div>
      </mbutton>

    <?php } else { ?>

      <mbutton material has-icon=left has-icon=left size=mid tag filled>
        <div fl alic gap=smol has-tooltip=bottom z curwhat>
          <mi mid style=margin-left:-.2em;>globe</mi>
          <p text bold><?= $rankings->global; ?></p>
          <p>
            <?php

            if ($rank_development["global"]["performance"] > 0)
              echo '<mi color=red>trending_down</mi>';
            else if ($rank_development["global"]["performance"] < 0)
              echo '<mi color=green>trending_up</mi>';
            else
              echo '<mi slight>remove</mi>';

            ?>
          </p>
          <div ttooltip>
            <p text>
              <strong>Global</strong> - <?= $mode . " " . $current_mod; ?>
            </p>
          </div>
        </div>

        <div dot-divider></div>

        <!--- Country Ranking --->
        <div fl alic gap=smol has-tooltip=bottom z curwhat>
          <?php if ($User->country == "xx") { ?>
            <mi>circle</mi>
          <?php } else { ?>
            <picture size=smol circled mr=smol>
              <?php $User->country_icon(); ?>
            </picture>
          <?php } ?>
          <p text bold><?= $rankings->country; ?></p>
          <p>
            <?php

            if ($rank_development["country"]["performance"] > 0)
              echo '<mi color=red>trending_down</mi>';
            else if ($rank_development["country"]["performance"] < 0)
              echo '<mi color=green>trending_up</mi>';
            else
              echo '<mi slight>remove</mi>';

            ?>
          </p>
          <div ttooltip>
            <p text>
              <strong><?= $User->country_string(); ?></strong> -
              <?= $mode . " " . $current_mod; ?>
            </p>
          </div>
        </div>
      </mbutton>

    <?php } ?>
  </div>

<?php } ?>