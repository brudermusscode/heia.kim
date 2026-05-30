<?php

/**
 * URL basication
 */
$base_url ??= "";
$url_filter ??= "";

/**
 * Append slash to the beginning of the url filter if there is none
 */
if ($url_filter && $url_filter[0] !== "/")
  $url_filter = "/" . $url_filter;

/**
 * All pages count.
 */
$ppages = ceil($pcount / $limit);

/**
 * Count of pages to show left and right.
 */
$show_pages = 2;
$startPage = max(1, $ppage - $show_pages);
$endPage = min($ppage + $show_pages, $ppages);

?>

<div class=pagination rounded=widest fl gap=smol alic>

  <?php if ($ppage > $show_pages + 1) { ?>
    <a href="<?= "$base_url/1" . $url_filter; ?>" sub scroll-top>
      <div has-tooltip=left>
        <div class=pagination__option navigation fl jucc alic bold>
          <i class=mi>first_page</i>
        </div>
        <div ttooltip>
          <p text std bold>First page</p>
        </div>
      </div>
    </a>
  <?php } ?>

  <?php

  /**
   * Previous pages tools.
   */
  if ($ppage > 1) {
    $previousPage = $ppage - 1;

  ?>

    <a href="<?= "$base_url/$previousPage" . $url_filter; ?>" sub scroll-top>
      <div class=pagination__option navigation fl jucc alic bold>
        <i class=mi>chevron_left</i>
      </div>
    </a>

  <?php

  }

  /**
   * Pages shown.
   */
  for ($i = $startPage; $i <= $endPage; $i++) { ?>

    <a href="<?= "$base_url/$i" . $url_filter; ?>" sub scroll-top>
      <div class=pagination__option fl jucc alic bold <?php if ($ppage === $i) echo " active"; ?>>
        <p text midler><?= $i; ?></p>
      </div>
    </a>

  <?php

  }

  /**
   * Next pages tools.
   */

  if ($ppage < $ppages) {
    $nextPage = $ppage + 1; ?>

    <a href="<?= "$base_url/$nextPage" . $url_filter; ?>" sub scroll-top>
      <div class=pagination__option navigation fl jucc alic bold>
        <i class=mi>chevron_right</i>
      </div>
    </a>

  <?php } ?>

  <?php if ($ppage < $ppages - $show_pages) { ?>
    <a href="<?= "$base_url/$ppages" . $url_filter; ?>" sub scroll-top>
      <div has-tooltip=right>
        <div class=pagination__option navigation fl jucc alic bold>
          <i class=mi>last_page</i>
        </div>
        <div ttooltip>
          <p text std bold>Last page</p>
        </div>
      </div>
    </a>
  <?php } ?>

</div>