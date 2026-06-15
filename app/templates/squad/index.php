<?php

use Heiakim\Model\Gamemode;
use Heiakim\Model\Squad;

/**
 * @var string
 */
$mode = filter_var(GET->mode ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Validate mode.
 */
if (!in_array($mode, Gamemode::$modes_text))
  $mode = null;

/**
 * @var string
 */
$mode_string = $mode ? Gamemode::mode_full($mode) : null;

/**
 * Publicity filter.
 */
$publicity = GET->publicity ?? null;
$publicity =
  $publicity !== null && in_array($publicity, Squad::$joinable)
  ? $publicity
  : null;



if ($publicity !== null) {
  $publicity_array_string = array_search($publicity, Squad::$joinable);
  $publicity_string = $publicity_array_string == "request" ? "On request only" : $publicity_array_string;
}

/**
 * Basication.
 */
$base_url = "/squads";
$url_query = $publicity === null ? "" : "?publicity=$publicity";

/**
 * @var int
 */
$limit = 23;

/**
 * @var int
 */
$offset = 0;

?>

<header page scroll-manipulated>
  <div class=title>
    <div fl align-items=center fldircol>
      <h1 text wide bold title>Squads</h1>
      <section>
        <p text smol tac><?= __("Join a squad and enjoy playing together") ?></p>
      </section>
    </div>
  </div>

  <div fl jucc alic gap=smol>
    <div class="outer">
      <a href="<?= "$base_url$url_query"; ?>" sub>
        <div ripple-effect rounded="mid" class="option" <?php display_active($mode, null) ?>>
          <p class="icon">
            <mi>all_inclusive</mi>
          </p>
          <p class="text" hide-700><?= __("All") ?></p>
        </div>
      </a>
      <a href="<?= "$base_url/osu$url_query"; ?>" sub>
        <div ripple-effect rounded="mid" class="option" <?php display_active($mode, "osu") ?>>
          <p class="icon">
            <i class="osu-icon osu-vanilla"></i>
          </p>
          <p class="text" hide-700><?= __("Standard") ?></p>
        </div>
      </a>
      <a href="<?= "$base_url/ctb$url_query"; ?>" sub>
        <div ripple-effect rounded="mid" class="option" <?php display_active($mode, "ctb") ?>>
          <p class="icon">
            <i class="osu-icon osu-ctb"></i>
          </p>
          <p class="text" hide-700>Catch</p>
        </div>
      </a>
      <a href="<?= "$base_url/taiko$url_query"; ?>" sub>
        <div ripple-effect rounded="mid" class="option" <?php display_active($mode, "taiko") ?>>
          <p class="icon">
            <i class="osu-icon osu-taiko"></i>
          </p>
          <p class="text" hide-700>Taiko</p>
        </div>
      </a>
      <a href="<?= "$base_url/mania$url_query"; ?>" sub>
        <div ripple-effect rounded="mid" class="option" <?php display_active($mode, "mania") ?>>
          <p class="icon">
            <i class="osu-icon osu-mania"></i>
          </p>
          <p class="text" hide-700>Mania</p>
        </div>
      </a>

      <?php if (!CurrentUser->squad && !CurrentUser->is_socially_excluded()) { ?>
        <div ripple-effect rounded=mid class="option" create data-action="popup:open" data-href="/squad/new" has-tooltip=bottom>
          <p class=icon><i class="ri-add-fill"></i></p>
          <p class=text><?= __("Create") ?></p>

          <div ttooltip>
            <p text bold><?= __("Create new squad") ?></p>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</header>

<div header-page-content content-width=widest fl fldircol content-gap>
  <div class="page_filter" filter click-to-add style=z-index:102;>

    <!--- INFINITE SCROLL PARAMETER --->
    <form data-form="infinite-scroll" data-form-type=beatmaps fl jucstart>

      <div class="option">
        <p class=title><?= __("Publicity") ?></p>

        <mselect filled=lighter size=mid align=center clickable mselect-type=visible>
          <div class=mselect__inr fl align-items=center gap=smol+>
            <p mselect-visible-value text bold>
              <?= $publicity === null ? __("All") : ucfirst($publicity_string); ?>
            </p>
            <mi smol>expand_all</mi>
          </div>

          <mselect-dropdown>
            <div get-size>
              <div class=msd__inr>
                <a sub href="<?= "/squads"  . ($mode ? "/$mode" : ""); ?>">
                  <mselect-option mselect-input-value mselect-change-visible-value>
                    <p><?= __("All") ?></p>
                  </mselect-option>
                </a>

                <?php

                foreach (Squad::$joinable as $joinable => $state) {
                  if ($joinable === "request")
                    $joinable = "On request only";

                  /**
                   * Create a dynamic link.
                   */
                  $link_string = "/squads" . ($mode ? "/$mode" : "") . "?publicity=$state";

                ?>
                  <a sub href="<?= $link_string; ?>">
                    <mselect-option mselect-input-value mselect-change-visible-value>
                      <p><?= __(ucfirst($joinable)); ?></p>
                    </mselect-option>
                  </a>
                <?php } ?>

              </div>
            </div>
          </mselect-dropdown>
        </mselect>
      </div>

      <?php if ($publicity !== null) { ?>
        <input type=hidden name=joinable value="<?= $publicity; ?>" />
      <?php } ?>

      <input name='mode' value="<?= $mode; ?>" type="hidden" />
      <input name='limit' value="<?= $limit; ?>" type="hidden" />
      <input data-react="infinite-scroll-offset" name='offset' value="<?= $limit; ?>" type="hidden" />

      <button type=submit name=ass>submit</button>
    </form>
  </div>

  <?php

  /**
   * @var Squad
   */
  $Squads = Squad::with("members.user")
    ->withCount('members')
    ->when($publicity !== null, function ($query) use ($publicity) {
      $query->where("joinable", $publicity);
    })
    ->when($mode, function ($q) use ($mode) {
      $q->whereJsonContains('modes->' . $mode, 1); // chillig.
    })
    ->orderByDesc("members_count")
    ->limit($limit)
    ->offset($offset)
    ->orderByDesc("created_at")
    ->get();

  ?>

  <div>
    <!--- SQUADS CONTENT --->
    <div fl fldircol gap=mid>
      <div fl fldircol gap>
        <div fl fldircol gap=smolest title-inline>
          <p text mid bold><?= __("Most popular") ?></p>
          <p text std slight><?= __("Showing Squads with the most members") ?></p>
        </div>
        <div flex-with-3>
          <?php

          if ($Squads->count())
            foreach ($Squads as $key => $Squad) {

              /**
               * Just show 3 Squads.
               */
              if ($key == 3) break;

              $set_appart = true;
              include __DIR__ . "/_squad.php";
            }
          else {

          ?>
            <box-model rounded=wide filled=lighter p62 fl fldircol alic gap style=flex:1;>
              <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
                <?php if (in_array($mode, Gamemode::$modes_text)) { ?>
                  <i text wide class="osu-icon osu-<?= $mode == "osu" ? "vanilla" : $mode; ?>"></i>
                <?php } else { ?>
                  <i class="mi" size="wide">diversity_3</i>
                <?php } ?>
              </div>
              <div tac>
                <p text bold wide><?= __("Nothing") ?></p>
                <p text std><?= __("There is no squad in here") ?></p>
              </div>
            </box-model>
          <?php } ?>
        </div>
      </div>

      <?php if ($Squads->count() > 3) { ?>
        <div fl fldircol gap>
          <div fl fldircol gap=smolest title-inline>
            <p text mid bold><?= ucfirst(__("others")) ?></p>
          </div>
          <div grid-repeat gap=smol jucstart scroll=infinite scroll-type=squads>
            <?php

            foreach ($Squads->skip(3) as $key => $Squad)
              include __DIR__ . "/_squad.php";

            ?>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php

include TEMPLATE . "/global/_scroll_end_logo.php";
