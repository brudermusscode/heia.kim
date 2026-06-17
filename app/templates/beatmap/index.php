<?php

use Heiakim\Model\Beatmap;
use Heiakim\Model\Beatmap\Set;
use Heiakim\Model\Gamemode;
use Illuminate\Support\Collection;

$mode   = filter_var($_GET["mode"] ?? "all", FILTER_SANITIZE_SPECIAL_CHARS);
$status = filter_var($_GET["status"] ?? "all", FILTER_SANITIZE_SPECIAL_CHARS);
$order  = filter_var($_GET["order"] ?? "id", FILTER_SANITIZE_SPECIAL_CHARS);
$filter = filter_var($_GET["filter"] ?? "all", FILTER_SANITIZE_SPECIAL_CHARS);
$query  = filter_var($_GET["query"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

$default_get = false;
if (count($_GET) < 2) $default_get = true;

# Serializing mode.
if (!in_array($mode, Gamemode::$modes_text, true))
  $mode = "all";

$mode_text = match ($mode) {
  "osu" => __("Standard"),
  "taiko" => "Taiko",
  "ctb" => "Catch the Beat",
  "mania" => "Mania",
  default => "All",
};

# Serializing status.
if (!in_array($status, Beatmap::$status_text, true))
  $status = "all";

$status_text = match ($status) {
  "ranked" => "Ranked",
  "approved" => __("Approved"),
  "qualified" => __("Qualified"),
  "loved" => "Loved",
  "pending" => __("Pending"),
  default => __("All"),
};

# Serializing the list order.
if (!in_array($order, Beatmap::$orders, true))
  $order = "id";

$order_text = match ($order) {
  "title" => __("Title"),
  "artist" => __("Artist"),
  "plays" => __("Most played"),
  default => __("Newest"),
};

$fetch_count = 20;

/**
 * @var Collection<Set>
 */
$BeatmapSets = Set::view(
  query: $query,
  status: $status,
  mode: $mode,
  order: $order,
  filter: $filter,
  limit: $fetch_count
);

$base_url = "/beatmaps";
$append_query_string  = $query ? "?query=$query" : "";
$has_searches = LOGGED && CurrentUser->searches->count();

# Partial inclusion.
include TEMPLATE . "/home/_page-navigator.php";
include __DIR__ . "/_header.php"; ?>

<filters>
  <div content-width=smoler style="height:100vh;" z fl fldircol jucc>
    <div fl justify-content=start mb>
      <mbutton ripple-effect overlay-close background=invert rounded=wide color=invert>
        <div fl align-items="center" gap="smol">
          <p text smol><strong>ESC</strong> <?= __("to close") ?></p>
        </div>
      </mbutton>
    </div>

    <box-model filled elevated animation="zoom-in">
      <bm-inr size=mid fl fldircol gap=mid>

        <div fl fldircol gap=smol+>
          <p text bold>Gamemode</p>

          <div fl gap=smol flex-wrap=wrap>
            <a sub href="<?= "$base_url/osu/$status/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($mode == "osu") echo "active"; ?> fl gap=smol alic>
                <i class="osu-icon osu-vanilla" text></i>
                <p text std bold><?= __("Standard") ?></p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/taiko/$status/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($mode == "taiko") echo "active"; ?> fl gap=smol alic>
                <i class="osu-icon osu-taiko" text></i>
                <p text std bold>Taiko</p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/ctb/$status/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($mode == "ctb") echo "active"; ?> fl gap=smol alic>
                <i class="osu-icon osu-ctb" text></i>
                <p text std bold>Catch the Beat</p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/mania/$status/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($mode == "mania") echo "active"; ?> fl gap=smol alic>
                <i class="osu-icon osu-mania" text></i>
                <p text std bold>Mania</p>
              </mbutton>
            </a>
          </div>
        </div>

        <div fl fldircol gap=smol+>
          <p text bold>Status</p>

          <div fl gap=smol flex-wrap=wrap>
            <a sub href="<?= "$base_url/$mode/ranked/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($status == "ranked") echo "active"; ?> fl gap=smol alic>
                <p text std bold>🏅 &nbsp;Ranked</p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/loved/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($status == "loved") echo "active"; ?> fl gap=smol alic>
                <p text std bold>❤️ &nbsp;Loved</p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/approved/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($status == "approved") echo "active"; ?> fl gap=smol alic>
                <p text std bold>✅ &nbsp;<?= __("Approved") ?></p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/qualified/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($status == "qualified") echo "active"; ?> fl gap=smol alic>
                <p text std bold>🔖 &nbsp;<?= __("Qualified") ?></p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/pending/$order/$filter" . $append_query_string;  ?>">
              <mbutton has-icon=left filled=darker <?php if ($status == "pending") echo "active"; ?> fl gap=smol alic>
                <p text std bold>⏳ &nbsp;<?= __("Pending") ?></p>
              </mbutton>
            </a>
          </div>
        </div>

        <div fl fldircol gap=smol+>
          <p text bold><?= __("Sort by") ?></p>

          <div fl gap=smol flex-wrap=wrap>
            <a sub href="<?= "$base_url/$mode/$status/id/$filter" . $append_query_string;  ?>">
              <mbutton filled=darker <?php if (isset($_GET["order"]) && $order == "id") echo "active"; ?> fl gap=smol alic>
                <p text std bold><?= __("Newest") ?></p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/$status/title/$filter" . $append_query_string;  ?>">
              <mbutton filled=darker <?php if ($order == "title") echo "active"; ?> fl gap=smol alic>
                <p text std bold><?= __("Title") ?></p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/$status/artist/$filter" . $append_query_string;  ?>">
              <mbutton filled=darker <?php if ($order == "artist") echo "active"; ?> fl gap=smol alic>
                <p text std bold><?= __("Artist") ?></p>
              </mbutton>
            </a>

            <a sub href="<?= "$base_url/$mode/$status/plays/$filter" . $append_query_string;  ?>">
              <mbutton filled=darker <?php if ($order == "plays") echo "active"; ?> fl gap=smol alic>
                <p text std bold><?= __("Most played") ?></p>
              </mbutton>
            </a>
          </div>
        </div>
      </bm-inr>
    </box-model>
  </div>
</filters>

<div content-width=widest fl fldircol content-gap>
  <div title-inline fl gap jucsb>


    <!--- CURRENTLY ACTIVE FILTERS --->
    <div fl gap=smol alic>
      <?php

      /**
       * @var string
       */
      $tv_size_filter_url = "$base_url/$mode/$status/$order/" . ($filter !== "tvsize" ? "tvsize" : "all") . $append_query_string;

      ?>
      <a sub href="<?= $tv_size_filter_url; ?>">
        <mbutton has-icon=left filled=slight <?php if ($filter == "tvsize") echo "active"; ?> fl gap=smol alic>
          <mi>history_toggle_off</mi>
          <p text bold><?= __("TV Size") ?></p>
        </mbutton>
      </a>

      <?php if ($mode !== "all" || $status !== "all" || isset($_GET["order"])) { ?>
        <div class=divide style=margin-inline:1.2em;></div>
      <?php } ?>

      <?php if ($mode !== "all") { ?>
        <a sub href="<?= "$base_url/all/$status/$order/$filter" . $append_query_string;  ?>">
          <mbutton has-icon=left filled=slight active fl gap=smol alic>
            <mi>remove</mi>
            <p text bold><?= $mode_text; ?></p>
          </mbutton>
        </a>
      <?php } ?>

      <?php if ($status !== "all") { ?>
        <a sub href="<?= "$base_url/$mode/all/$order/$filter" . $append_query_string;  ?>">
          <mbutton has-icon=left filled=slight active fl gap=smol alic>
            <mi>remove</mi>
            <p text bold>
              <?= ($status == "ranked" ? "🏅 &nbsp;" : ($status == "loved" ? "❤️ &nbsp;" : ($status == "approved" ? "✅ &nbsp;" : ($status == "pending" ? "⏳ &nbsp;" : "🔖 &nbsp;")))) . " " . $status_text; ?>
            </p>
          </mbutton>
        </a>
      <?php } ?>

      <?php if (isset($_GET["order"])) { ?>
        <a sub href="<?= "$base_url/$mode/$status/all/$filter" . $append_query_string;  ?>">
          <mbutton has-icon=left filled=slight active fl gap=smol alic>
            <mi>remove</mi>
            <p text bold><?= $order_text; ?></p>
          </mbutton>
        </a>
      <?php } ?>
    </div>

    <div fl gap=smol alic>
      <a data-action="filters:open">
        <mbutton has-icon=left filled=slight fl gap=smol alic>
          <mi>filter_list</mi>
          <p text bold>Filter</p>
        </mbutton>
      </a>
    </div>
  </div>

  <div>

    <!--- INFINITE SCROLL PARAMETER --->
    <form data-form="infinite-scroll" data-form-type=beatmaps>
      <input type=hidden name=submit_link value="<?= "$base_url/$mode/$status/$order/$filter";  ?>" />
      <input name='query' value="<?= $query; ?>" type="hidden" />
      <input name='filter' value="<?= $filter; ?>" type="hidden" />
      <input name='backup' backup value="<?= $query; ?>" type="hidden" />
      <input name='mode' value="<?= $mode; ?>" type="hidden" />
      <input name='status' value="<?php if (is_array($status)) echo implode(',', $status);
                                  else echo $status; ?>" type="hidden" />
      <input name='order' value="<?= $order; ?>" type="hidden" />
      <input name='limit' value="<?= $fetch_count; ?>" type="hidden" />
      <input data-react="infinite-scroll-offset" name='offset' value="<?= $fetch_count; ?>" type="hidden" />

      <button type=submit name=ass>submit</button>
    </form>

    <!--- BEATMAPS CONTENT --->
    <div grid-repeat gap="smol" data-react="beatmaps:search" scroll=infinite scroll-type=beatmaps>
      <?php

      /**
       * Needed to show all difficulties on the beatmap container.
       * If this is not set, it will fallback to false and only show one
       * difficulty which might endup in an error
       */
      $include_all_diffs = true;

      if (!$BeatmapSets->count())
        include COMPONENT . '/ui/_none.html';
      else
        foreach ($BeatmapSets as $key => $Set)
          include dirname(__DIR__) . "/beatmapset/_set-card.php";

      ?>
    </div>
  </div>
</div>

<?php include TEMPLATE . "/global/_scroll_end_logo.php"; ?>