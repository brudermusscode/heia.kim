<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\Leaderboard;

/**
 * @var string
 */
$mode = filter_var($_GET["mode"] ?? "osu", FILTER_SANITIZE_SPECIAL_CHARS);
$mode = !in_array($mode, Gamemode::$modes_text) ? "osu" : $mode;

/**
 * @var string
 */
$mod = filter_var($_GET["mod"] ?? "vanilla", FILTER_SANITIZE_SPECIAL_CHARS);
$mod = !in_array($mod, Gamemode::$mods_text) ? "vanilla" : $mod;

$fck_mod = $mod;

/**
 * @var string
 */
$type = filter_var($_GET["type"] ?? "performance");
$type = !in_array($type, Leaderboard::$types) ? "performance" : $type;

/**
 * @var string
 */
$model = filter_var($_GET["model"] ?? "players");
$model = !in_array($model, Leaderboard::$models) ? "players" : $model;

/**
 * @var string
 */
$country = filter_var($_GET["country"] ?? "global");

# Sorting by the scoring type.
$sort_by = match ($type) {
  "score" => "rscore",
  default => "pp",
};

# The currently viewed mode + mod as an int.
$gumode = Gamemode::get_gumode_as_int($mode, $mod);

/**
 * @var Leaderboard
 */
$Leaderboard = new Leaderboard;

# We've got unknown keys in the leaderboard array since they represent the user's id
# with a score as the value. Therefore we need a counter that will increase by one
# whenever a new user from the leaderboard is being worked with.
$leaderboard_counter = 0;

# Cache newly added countries and store them in the database.
if ($model === "players")
  $Leaderboard->update_countries();


# Build the link base.
$base_url = "/leaderboard";
$link_add_type = $type ? "/$type" : "";
$link_add_country = $country ? "?country=$country" : "";

# Build pagination.
$ppage  = filter_var($_GET["ppage"] ?? 1, FILTER_VALIDATE_INT);
$limit = 50;
$offset = $ppage === 1
  ? ($ppage - 1) * $limit
  : ($ppage - 1) * $limit + 1;

# + Big header.
include __DIR__ . "/_header.php"; ?>

<div page-structure="leaderboard">
  <div column-wrapper>

    <div column=smaller style=top:142px; fl fldircol gap=mid>
      <?php

      # + Menu with different leaderboard types.
      include __DIR__ . "/_menu.php"; ?>
    </div>

    <div column=large flexone fl fldircol gap=smol>
      <?php if (!$LeaderboardUsers) : ?>

        <box-model rounded=wide filled=lighter>
          <bm-inr p62>
            <div fl fldircol alic jucc gap>
              <div circled style=min-height:4.2em;width:4.2em; filled fl alic jucc>
                <i class=mi text wide>face</i>
              </div>
              <div tac>
                <p text wide bold><?= __("Nothing") ?></p>
                <p text std><?= __("There are no players on this board.") ?></p>
              </div>
            </div>
          </bm-inr>
        </box-model>

      <?php else :

        $file_path = __DIR__ . "/pages/_$model.php";
        $file_exists = file_exists($file_path);

        include $file_exists ? $file_path : __DIR__ . "/pages/_players.php";

      ?>

      <?php endif; ?>

      <div fl jucc>
        <div fl gap=smol flex-wrap=wrap alic>
          <?php

          $base_url = "$base_url/$mode/$mod/$type/$country";

          include COMPONENT . "/_pagination.php"; ?>
        </div>
      </div>
    </div>

    <div column=small hide-tablet>
    </div>
  </div>
</div>

<?php

# + Scroll ending with logo.
include TEMPLATE . "/global/_scroll_end_logo.php";
