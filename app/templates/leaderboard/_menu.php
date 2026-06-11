<?php

use Heiakim\Model\Country;

/**
 * @var string $country
 * @var string $model
 * @var string $mode
 * @var string $mod
 */

?>

<div fl fldircol gap=smol+>
  <p text bold>Leaderboard</p>
  <inline-navigator fl fldircol gap=smoler>
    <a href="<?= "$base_url/players/osu/vanilla/performance"; ?>" sub fl alic jucsb
      <?php if ($model !== "squads") echo "active"; ?>>
      <p text bold>Players</p>
      <in-o-icon>
        <mi>groups_3</mi>
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40">
          <path d="M.41 12.649C-1.78 5.166 5.166-1.781 12.65.41l4.579 1.342c1.81.53 3.734.53 5.544 0L27.352.41C34.833-1.78 41.781 5.166 39.59 12.65l-1.342 4.579a9.864 9.864 0 0 0 0 5.544l1.342 4.58c2.191 7.482-4.756 14.43-12.239 12.238l-4.578-1.342a9.864 9.864 0 0 0-5.546 0L12.65 39.59C5.166 41.78-1.781 34.834.41 27.35l1.342-4.578c.53-1.81.53-3.735 0-5.546L.41 12.65Z"></path>
        </svg>
      </in-o-icon>
    </a>

    <a href="<?= "$base_url/squads/osu/vanilla/performance"; ?>" sub fl alic jucsb
      <?php display_active($model, "squads"); ?>>
      <p text bold>Squads</p>
      <in-o-icon>
        <mi>workspaces</mi>
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40">
          <path d="M.41 12.649C-1.78 5.166 5.166-1.781 12.65.41l4.579 1.342c1.81.53 3.734.53 5.544 0L27.352.41C34.833-1.78 41.781 5.166 39.59 12.65l-1.342 4.579a9.864 9.864 0 0 0 0 5.544l1.342 4.58c2.191 7.482-4.756 14.43-12.239 12.238l-4.578-1.342a9.864 9.864 0 0 0-5.546 0L12.65 39.59C5.166 41.78-1.781 34.834.41 27.35l1.342-4.578c.53-1.81.53-3.735 0-5.546L.41 12.65Z"></path>
        </svg>
      </in-o-icon>
    </a>
  </inline-navigator>
</div>

<div fl fldircol gap=smol+>
  <p text bold>Sort by</p>
  <div fl fldircol gap=smol>
    <a href="<?= "$base_url/$model/$mode/$mod/performance"; ?>">
      <mbutton material size=mid filled=lighter has-icon=left <?php if ($type == "performance") echo "active"; ?>>
        <mi><?= METRIC_ICON; ?></mi>
        Performance
      </mbutton>
    </a>

    <a href="<?= "$base_url/$model/$mode/$mod/score"; ?>">
      <div fl alic gap=smol rounded=wide pl12 pblock12 pr18 filled=lighter has-icon=left <?php if ($type == "score") echo "active"; ?>>
        <mi>trending_up</mi>
        Score
      </div>
    </a>
  </div>
</div>

<?php

# + Country icon.
if ($country !== "global") :
  $Country = Country::where("abbreviation", $country)->first();

?>
  <div fl fldircol gap=smol+>
    <p text bold>Country</p>
    <a href="<?= "$base_url/$model/$mode/$mod/$type"; ?>">
      <mbutton material size=mid background="slight" has-icon=left active>
        <mi>remove</mi>
        <picture size=smoler circled fl alic jucc>
          <?php $Country->icon(); ?>
        </picture>
        <p text std bold><?= $Country->display(); ?></p>
      </mbutton>
    </a>
  </div>
<?php endif; ?>

<div fl fldircol gap=smol+>
  <p text bold>Mode</p>
  <div posrel>
    <sub-stick-menu>
      <a <?= $fck_mod === "vanilla" ? "active" : ""; ?>
        href="<?= "$base_url/$model/$mode/vanilla/$type?country=$country"; ?>">
        <div class=sm__option hoverable>
          <mi>done</mi>
          <p text bold>Vanilla</p>
        </div>
      </a>

      <?php if (in_array($mode, ["osu", "taiko", "ctb"])) { ?>
        <a <?php if ($mod === "relax") echo "active"; ?>
          href="<?= "$base_url/$model/$mode/relax/$type?country=$country"; ?>">
          <div class=sm__option hoverable>
            <mi>done</mi>
            <p text bold>Relax</p>
          </div>
        </a>
      <?php } ?>

      <?php if (in_array($mode, ["osu"])) { ?>
        <a <?php if ($mod === "autopilot") echo "active"; ?>
          href="<?= "$base_url/$model/$mode/autopilot/$type?country=$country"; ?>">
          <div class=sm__option hoverable>
            <mi>done</mi>
            <p text bold>Autpilot</p>
          </div>
        </a>
      <?php } ?>
    </sub-stick-menu>
  </div>
</div>