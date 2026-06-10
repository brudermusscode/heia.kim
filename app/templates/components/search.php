<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;

/**
 * @var Request $Request
 */

# Start the output buffer.
ob_start(); ?>

<global-search animation=fade-in>
  <search fl alic elevated=smol rounded>
    <mi pinline42 mid>search</mi>

    <input search autofocus type-to-focus type="text" placeholder="<?= __("Search for players, beatmaps, squads") ?>...">

    <div fl alic gap=smol no-word-wrap>
      <p background=slight rounded=smol pblock4 pinline8 text smol bold>Esc</p>
      <p text smol bold><?= __("to close") ?></p>
    </div>

    <div loader class="linear-progress-material">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </div>
  </search>

  <search-results fl fldircol gap=mid></search-results>
</global-search>

<?php die(success(data: ob_get_clean()));
