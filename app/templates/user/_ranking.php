<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub_page
 * @var string $more
 * @var string $current_mod
 * @var string $mode
 * @var string $mod
 * @var int $gumode
 * @var bool $is_champion
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var bool $both_sides_can_interact_socially
 */

# + User has not yet played.
if (!$has_played) : ?>

  <mbutton material has-icon=left has-icon=right size=mid tag filled>
    <mi>domino_mask</mi>
    <p text bold smol ttup trimt>Never played</p>

    <div dot-divider></div>

    <div fl alic gap=smol>
      <?php if ($User->country == "xx") : ?>
        <mi>domino_mask</mi>
      <?php else : ?>
        <picture size=smol circled>
          <?php $User->country()->first()->icon(); ?>
        </picture>
      <?php endif; ?>
      <p text smol bold ttup><?= $User->country_string(); ?></p>
    </div>
  </mbutton>

<?php

# + User has played already.
else : ?>

  <div fl alic gap=smol+ z>
    <div fl alic gap=smol has-tooltip=bottom z curwhat>
      <!-- <mi midler><?= METRIC_ICON; ?></mi> -->
      <p text mid><?= number_format($Stats->pp); ?> pp</p>
      <div ttooltip>
        <p text bold>Performance Points</p>
      </div>
    </div>


    <?php

    # + User is champion! (#1)
    if ($is_champion) : ?>

      <mbutton material has-icon=left has-icon=right size=mid tag background=special color=light has-tooltip=bottom curwhat>
        <mi mid>globe</mi>
        <p text bold smol ttup>Champion</p>
        <div ttooltip>
          <p text>
            <strong>#1 Global</strong> - <?= $mode . " " . $current_mod; ?>
          </p>
        </div>
      </mbutton>

    <?php

    # + User achieved a normal rank.
    else : ?>

      <mbutton material has-icon=left has-icon=left size=mid tag filled>
        <div fl alic gap=smol has-tooltip=bottom z curwhat>
          <mi mid style=margin-left:-.2em;>globe</mi>
          <p text bold><?= $rankings->global; ?></p>
          <p>
            <?php

            if ($rank_development->global->performance > 0)
              echo '<mi color=red>trending_down</mi>';
            else if ($rank_development->global->performance < 0)
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

            if ($rank_development->country->performance > 0)
              echo '<mi color=red>trending_down</mi>';
            else if ($rank_development->country->performance < 0)
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

    <?php endif; ?>
  </div>

<?php endif; ?>