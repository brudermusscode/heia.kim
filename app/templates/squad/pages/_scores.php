<?php

use Heiakim\Model\Gamemode;
use Heiakim\Utils\Utils;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var Squad $Squad
 * @var string $id
 * @var string $sub
 * @var string $page
 * @var string $base_url
 */

$mode = $sub;

if (!in_array($mode, Gamemode::$modes_text))
  $mode = "osu";

$mod = in_array($sub, Gamemode::$mods_text) ? $sub : Gamemode::$mods_text[0];
$gumode = Gamemode::find_gumode($mode, $mod);

# Serialize mod.
$mod = $mode === "osu" && !in_array($sub, ["vanilla", "relax", "autopilot"])
  || $mode === "taiko" && !in_array($sub, ["vanilla", "relax"])
  || $mode === "ctb" && !in_array($sub, ["vanilla", "relax"])
  || $mode === "mania" && !in_array($sub, ["vanilla"])
  ? "vanilla"
  : $sub;

/**
 * @var int[]
 */
$modes = $mod
  ? [Gamemode::find_gumode($mode, $mod)]
  : Gamemode::$mods_int_per_mode[$mode];

# Fallback to the first gumode inside the given mode.
$gumode ??= Gamemode::$mods_int_per_mode[$mode][0];

?>

<div page-structure="squad">

  <?php include dirname(__DIR__) . "/_tabs.php"; ?>

  <div column-wrapper>
    <div column=small fl fldircol gap=mid>

      <?php

      /**
       * @var array
       */
      $Stats = $Squad->performance[$gumode] ?? Squad::$default_performance[$gumode];

      ?>

      <div fl fldircol gap=smol>
        <box-model user-stats outlined expand-more expand-more-show hoverable rounded=wide>
          <bm-inr size=std rounded=mid>
            <?php foreach ($Stats as $stat => $value) :
              if ($stat === "performance") : ?>
                <div fl jucsb alic>
                  <div>
                    <p text wide bold><?= number_format($value); ?></p>
                    <p text smol><?= ucwords(str_replace("_", " ", $stat)); ?> Points</p>
                  </div>
                  <mbutton icon-only hoverable>
                    <mi size="midler">keyboard_arrow_down</mi>
                  </mbutton>
                </div>
              <?php else : ?>
                <div fl expand-more-hidden
                  <?php if ($stat === "accuracy") echo "mt"; ?>>
                  <p text style="width:50%;">
                    <?= ucwords(str_replace("_", " ", $stat)); ?></p>
                  <p text bold color=active-text>
                    <?php if (in_array($stat, ["ranked_score", "total_score"]))
                      echo Utils::round_with_ending($value);
                    else if ($stat === "accuracy")
                      echo number_format($value, 2) . " %";
                    else
                      echo number_format($value); ?>
                  </p>
                </div>
            <?php endif;
            endforeach; ?>
          </bm-inr>
        </box-model>

        <inline-navigator fl fldircol gap=smoler>
          <a href="<?= "$base_url/$mode"; ?>" sub fl alic jucsb
            <?php display_active($sub); ?>>
            <div>
              <p text bold>All mods</p>
              <?php if (!$sub) : ?>
                <p text smol slight><?= $mode === "mania" ? "Showing <strong>Standard</strong>" : ($mode === "osu" ? "Showing <strong>Standard, Relax & Autopilot</strong>" : "Showing <strong>Standard & Relax</strong>"); ?></p>
              <?php endif; ?>
            </div>
            <in-o-icon>
              <mi>all_inclusive</mi>
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40">
                <path d="M.41 12.649C-1.78 5.166 5.166-1.781 12.65.41l4.579 1.342c1.81.53 3.734.53 5.544 0L27.352.41C34.833-1.78 41.781 5.166 39.59 12.65l-1.342 4.579a9.864 9.864 0 0 0 0 5.544l1.342 4.58c2.191 7.482-4.756 14.43-12.239 12.238l-4.578-1.342a9.864 9.864 0 0 0-5.546 0L12.65 39.59C5.166 41.78-1.781 34.834.41 27.35l1.342-4.578c.53-1.81.53-3.735 0-5.546L.41 12.65Z"></path>
              </svg>
            </in-o-icon>
          </a>

          <?php if (in_array($mode, ["osu", "taiko", "ctb"])) : ?>
            <a href="<?= "$base_url/$mode/vanilla"; ?>" sub fl alic jucsb <?php display_active($sub, "vanilla"); ?>>
              <p text bold>Standard</p>
            </a>
          <?php endif; ?>
          <?php if (in_array($mode, ["osu", "taiko", "ctb"])) : ?>
            <a href="<?= "$base_url/$mode/relax"; ?>" sub fl alic jucsb <?php display_active($sub, "relax"); ?>>
              <p text bold>Relax</p>
            </a>
          <?php endif; ?>
          <?php if (in_array($mode, ["osu"])) : ?>
            <a href="<?= "$base_url/$mode/autopilot"; ?>" sub fl alic jucsb <?php display_active($sub, "autopilot"); ?>>
              <p text bold>Autopilot</p>
            </a>
          <?php endif; ?>
        </inline-navigator>
      </div>
    </div>

    <div column=large fl fldircol flexone w100>
      <?php

      $Squad = $Squad->load(
        ['members.user.scores' => function ($query) use ($Squad, $modes) {
          $query
            ->whereIn("scores.mode", $modes)
            ->where('scores.play_time', '>', $Squad->created_at)
            ->whereIn("scores.status", [2])
            ->limit(20);
        }]
      );

      $Scores = collect();

      foreach ($Squad->members as $Member)
        $Scores = $Scores->merge($Member->user->scores);

      ?>

      <timeline>
        <?php if (!$Scores->count()) : ?>
          <div fl fldircol gap=smol style=flex:1;>
            <box-model rounded=wide filled=lighter p62 fl fldircol alic gap style=flex:1;>
              <div style="height:4.2em
              ;width:4.2em;" fl alic jucc circled filled>

                <i class="mi" size=wide>whatshot</i>
              </div>
              <div tac>
                <p text bold wide><?= __("Nothing") ?></p>
                <p text std><?= __("No scores have been set here so far") ?></p>
              </div>
              <?php if ($Squad->is_member(CurrentUser)) { ?>
                <div fl justify-content=center>
                  <a href="/beatmaps">
                    <mbutton mid has-icon=left background=dynamic has-icon>
                      <mi>explore</mi>
                      <p text std bold><?= __("Explore beatmaps") ?></p>
                    </mbutton>
                  </a>
                </div>
              <?php } ?>
            </box-model>
          </div>
        <?php else : ?>
          <t-object>
            <t-line></t-line>
            <?php foreach ($Scores as $Score)
              include dirname(__DIR__) . "/_score_post.php"; ?>
          </t-object>
        <?php endif; ?>
      </timeline>
    </div>

    <div column=small fl fldircol gap=mid hide-tablet>
      <?php

      /**
       * @var SquadUser
       */
      $HighestRankingMembers = $Squad->highest_ranking_members(gumode: $gumode, count: 4);

      if (!$HighestRankingMembers->count()) : ?>
        <box-model rounded="mid" outlined p32 fl fldircol alic gap>
          <div style="height:3.2em;width:3.2em;" fl alic jucc circled filled>
            <mi mid>social_leaderboard</mi>
          </div>
          <div tac>
            <p text bold midler>Top Performer</p>
            <p text color=company>Nothing to show</p>
          </div>
        </box-model>
      <?php else : ?>
        <div fl fldircol gap=smol+>
          <div fl alic gap=smol title-inline=smol>
            <p text midler bold>Top Performer</p>
          </div>

          <div fl fldircol gap=smoler>
            <?php

            $is_first = true;

            foreach ($HighestRankingMembers as $key => $Member) :
              $performance = (object) json_decode($Member->performance, true)[$gumode];

            ?>

              <a href="<?= $Member->user->link(); ?>">
                <div fl alic jucsb gap clickable active-rounder rounded=wide p12 <?= $is_first ? "filled" : "filled=lighter"; ?>>
                  <div fl alic gap=smol+>
                    <picture size=std circled>
                      <?php $Member->user->image(); ?>
                    </picture>
                    <div fl alic gap=smoler>
                      <p text bold><?= $Member->user->name(); ?></p>
                      <p text mid bold slighter style="margin-top:-.2em;">·</p>
                      <p text color=company><?= number_format($performance->performance); ?>pp</p>
                    </div>
                  </div>
                  <mi size=std>east</mi>
                </div>
              </a>

            <?php

              $is_first = false;

              /**
               * @var int
               */
              $last_member_performance = $performance->performance;
            endforeach; ?>

            <?php if ($Squad->members_count() > 5) : ?>
              <mbutton smol hoverable has-icon=right dno>
                <p text smol bold>+<?= $Squad->members_count() - 5; ?></p>
                <mi>east</mi>
              </mbutton>
            <?php endif; ?>
          </div>
        </div>

      <?php endif; ?>
    </div>

  </div>
</div>