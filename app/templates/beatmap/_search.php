<?php

/**
 * @var ?string $query
 */

?>

<search inline fl alic rounded=mid+>
  <mi>search</mi>

  <input data-action="beatmaps:search" type-to-focus type="text"
    placeholder="<?= __("Search for your favorite beatmaps"); ?>"
    value="<?= $query; ?>" />

  <div extra fl alic jucend gap=smol no-word-wrap>
    <p background=slight rounded=smol+ pblock6 pinline10 pr12 text smol bold fl alic gap=smol>
      <mi>keyboard</mi> ENTER
    </p>
  </div>

  <div loader class="linear-progress-material">
    <div class="bar bar1"></div>
    <div class="bar bar2"></div>
  </div>
</search>