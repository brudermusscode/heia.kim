<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Model\Artist;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Heiakim\Time\Time;
use Heiakim\Validate\Search;
use Illuminate\Support\Collection;

$query = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);
$fetch_limit = 8;

/**
 * @var Collection<User>
 */
$Users = User::whereRaw("MATCH(name, safe_name) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)])
  ->where("priv", ">", 2)
  ->limit($fetch_limit)
  ->orderBy("name")
  ->get();

/**
 * @var Collection<Beatmap\Set>
 */
$Sets = Beatmap\Set::whereHas("beatmaps", function ($q) use ($query) {
  $q->whereRaw("MATCH(title, artist, version, creator) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)]);
})
  ->with("beatmaps", function ($q) use ($query) {
    $q->whereRaw("MATCH(title, artist, version, creator) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)]);
  })
  ->limit($fetch_limit)
  ->orderByDesc("id")
  ->get();

/**
 * @var Collection<Squad>
 */
$Squads = Squad::whereRaw("MATCH(name, tag) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)])
  ->limit($fetch_limit)
  ->orderBy("name")
  ->get();

/**
 * @var Collection<Artist>
 */
$Artists = Artist::where("name", "LIKE", "%$query%")
  ->orderBy("name")
  ->limit(20)
  ->get();

$all_empty = !$Users->count()
  && !$Sets->count()
  && !$Squads->count()
  && !$Artists->count();

ob_start();

if ($all_empty) :
  echo '<box-model elevated>';
  include COMPONENT . '/ui/_none.html';
  echo '</box-model>';
else : ?>

  <?php

  # + Users
  if ($Users->count()) : ?>

    <div fl fldircol gap=smol+>
      <p title-inline text midler ttup bold color=company><?= __("Players") ?></p>

      <div fl jucstretch flex-wrap gap=smol>
        <?php foreach ($Users as $User) { ?>
          <a style="flex-basis:32.8%;min-width:32.8%;" href="<?= $User->link(); ?>">
            <div filled rounded p12 fl alic gap=smol+ clickable>
              <picture circled size=mid>
                <?php $User->image(); ?>
              </picture>

              <div>
                <p text std bold><?= $User->name(); ?></p>
                <p text smol timestamp>Last active &middot; <?= Time::ago(date('Y-m-d h:i:s', $User->latest_activity)); ?></p>
              </div>
            </div>
          </a>
        <?php } ?>
      </div>
    </div>
  <?php endif; ?>

  <?php

  # + Beatmapsets
  if ($Sets->count()) : ?>
    <div fl fldircol gap=smol+>
      <p title-inline text midler ttup bold color=company>Beatmaps</p>

      <div grid-repeat gap=smol>
        <?php

        foreach ($Sets as $Set)
          include TEMPLATE . "/beatmap/_beatmap-row.php";

        ?>
      </div>
      <div fl jucc mt=smol>
        <a href="/beatmaps?query=<?= str_replace("%", "", $query); ?>">
          <mbutton ripple-effect filled=lighter has-icon=right>
            <p text smol bold ttup><?= __("Show more") ?></p>
            <mi>east</mi>
          </mbutton>
        </a>
      </div>
    </div>
  <?php endif; ?>

  <?php

  # + Squads
  if ($Squads->count()) : ?>
    <div fl fldircol gap=smol+>
      <p title-inline text midler ttup bold color=company>Squads</p>

      <div grid-repeat gap=smol>
        <?php

        $mode = 0;

        foreach ($Squads as $Squad)
          include TEMPLATE . "/squad/_squad.php"; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php

  # + Artists
  if ($Artists->count()) : ?>
    <div fl fldircol gap=smol+>
      <p title-inline text midler ttup bold color=company><?= __("Artists") ?></p>

      <div grid-repeat gap=smol>
        <?php foreach ($Artists as $Artist) :
          include TEMPLATE . "/artist/_artist.php";
        endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

<?php endif;

die($Request->success(data: ob_get_clean()));
