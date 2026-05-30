<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * Begin output buffer
 */
ob_start();

?>

<div global-search>
  <div search class="search" animation=fade-in>
    <div class="search_outer">
      <div class="spacing">
        <div fl align-items="center" gap="smol">
          <div class="search_outer__icon" flcenter>
            <p text midler>
              <mi>search</mi>
            </p>
          </div>

          <div class="search_outer__input">
            <input data-action="search:start" type-to-focus type="text" placeholder="<?= __("Search for players, beatmaps, squads") ?>...">
          </div>

          <div fl gap>
            <div fl align-items=center gap=smol>
              <div background=slight rounded=smol>
                <div pblock8 pinline4 fl align-items=center gap=smol>
                  <div>
                    <p text smol bold>Esc</p>
                  </div>
                </div>
              </div>
              <p text smol bold><?= __("to close") ?></p>
            </div>
          </div>

          <!-- <div class="search_outer__icon" start flcenter>
            <p correct-top>
              <i class=mi>south_east</i>
            </p>
          </div> -->
        </div>
      </div>

      <div search-loader class="linear-progress-material">
        <div class="bar bar1"></div>
        <div class="bar bar2"></div>
      </div>
    </div>
  </div>
</div>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
