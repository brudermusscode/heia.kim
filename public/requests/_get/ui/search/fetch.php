<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Database\Manager as DBM;
use Heiakim\Model\Artist;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Squad;
use Heiakim\Model\User;
use Heiakim\Http\Request;
use Heiakim\Time\Time;
use Heiakim\Validate\Search;

/**
 * @var Request $Request
 */

/**
 * @var DBM
 */
$db = new DBM;

/**
 * @var string
 */
$query = filter_input(INPUT_POST, "query", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var int
 */
$fetch_limit = 8;

/**
 * @var User
 */
$Users = User::whereRaw("MATCH(name, safe_name) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)])
  ->where("priv", ">", 2)
  ->limit($fetch_limit)
  ->orderBy("name")
  ->get();

/**
 * @var Beatmap\Set
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
 * @var Squad
 */
$Squads = Squad::whereRaw("MATCH(name, tag) AGAINST(? IN BOOLEAN MODE)", [Search::ft_params_boolean_mode($query)])
  ->limit($fetch_limit)
  ->orderBy("name")
  ->get();

/**
 * @var Artist
 */
$Artists = Artist::where("name", "LIKE", "%$query%")
  ->orderBy("name")
  ->limit(20)
  ->get();

/**
 * Check if no result has come back and display something based
 * on this.
 *
 * @var bool
 */
$all_empty = !$Users->count() && !$Sets->count() && !$Squads->count() && !$Artists->count();

/**
 * Begin output buffer
 */
ob_start();

?>

<div class="global_search__result" fl fldircol gap=mid>

  <?php

  if ($all_empty) {
    echo '<box-model elevated>';
    include COMPONENT . '/ui/_none.html';
    echo '</box-model';
  } else {

  ?>

    <?php if ($Users->count()) { ?>

      <div fl fldircol gap=smol+>
        <div fl jucsb alic pblock24>
          <div>
            <p text mid bold><?= __("Players") ?></p>
          </div>
        </div>
        <box-model elevated rounded=wide filled>
          <bm-inr size=mid fl fldircol gap>
            <div fl fldircol gap=smol+>
              <div class="results" grid-repeat=smol>
                <?php foreach ($Users as $User) { ?>
                  <div grid-keeper>
                    <a href="<?= $User->link(); ?>">
                      <div class="option" user>
                        <div class="option_inr">
                          <div class="option_image">
                            <picture circled size=std>
                              <?php $User->image(); ?>
                            </picture>
                          </div>

                          <div class=" option_info">
                            <div class="name">
                              <p text std bold><?= $User->name(); ?></p>
                            </div>
                            <div class="activity">
                              <p text smol><?= Time::ago(date('Y-m-d h:i:s', $User->latest_activity)); ?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
                <?php } ?>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </div>
    <?php } ?>

    <?php if ($Sets->count()) { ?>
      <div fl fldircol gap=smol+>
        <div fl gap jucsb alic>
          <div>
            <p text mid bold>Beatmaps</p>
          </div>
        </div>
        <div grid-repeat gap=smol>
          <?php

          foreach ($Sets as $Set)
            include TEMPLATE . "/beatmapset/_set-card.php";

          ?>
        </div>
        <div fl jucc mt=smol>
          <a href="/beatmaps?query=<?= str_replace("%", "", $query); ?>">
            <mbutton material ripple-effect filled=lighter has-icon=right>
              <p text smol bold ttup><?= __("Show more") ?></p>
              <mi>east</mi>
            </mbutton>
          </a>
        </div>
      </div>
    <?php } ?>

    <?php if ($Squads->count()) { ?>
      <div fl fldircol gap=smol+>
        <div fl gap jucsb alic>
          <div>
            <p text mid bold>Squads</p>
          </div>
        </div>
        <div grid-repeat gap=smol>
          <?php

          foreach ($Squads as $Squad) {
            $mode = 0;
            include TEMPLATE . "/squads/_squad.php";
          }

          ?>
        </div>
      </div>
    <?php } ?>

    <?php if ($Artists->count()) { ?>
      <div fl fldircol gap=smol+>
        <div pblock24 fl alic jucsb>
          <div>
            <p text mid bold><?= __("Artists") ?></p>
          </div>
          <div style=display:none;>
            <mbutton material ripple-effect filled=lighter has-icon=right>
              <p text smol bold ttup><?= __("Show more") ?></p>
              <mi>east</mi>
            </mbutton>
          </div>
        </div>

        <box-model elevated rounded=wide filled>
          <bm-inr size=mid fl fldircol gap>
            <div fl fldircol gap=smol+>
              <div class="results" fl flex-wrap=wrap>
                <?php foreach ($Artists as $Artist) { ?>
                  <a href="/artist/<?= $Artist->id; ?>">
                    <div class="option" artist clickme>
                      <div class="textline" fl gap align-items=center flex-truncate>
                        <div flex-truncate>
                          <p text std bold trimt color><?= htmlspecialchars_decode($Artist->name); ?></p>
                        </div>
                      </div>
                    </div>
                  </a>
                <?php } ?>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </div>
    <?php } ?>

</div>

<?php

  }

  /**
   * * Success
   */
  die($Request->success(data: ob_get_clean()));
