<?php

$Searches = LOGGED ? $CurrentUser->searches()
  ->where("type", "beatmap")
  ->groupBy("search")
  ->orderByRaw("COUNT(search) DESC")
  ->limit(12)
  ->get()
  : null;

/**
 * Include main page navigator.
 */
include PAGE_NAVIGATOR;

?>

<header page scroll-manipulated>
  <div class=title>
    <div fl align-items=center fldircol>
      <h1 text wide bold title>Beatmaps</h1>
      <section>
        <p text smol tac><?= __("Scroll through our huge collection of beatmaps") ?></p>
      </section>
    </div>
  </div>

  <div content-width=std fl fldircol gap>
    <?php include_once __DIR__ . "/_search.php"; ?>

    <?php if (LOGGED && $Searches->count()) { ?>
      <div class=beatmaps__tags fl gap=smol flex-wrap=wrap jucc hide-scrolled hide-mobile>
        <?php

        foreach ($Searches as $Search) {
          $same_queries = $query && strtolower($query) == strtolower($Search->search);
          $search_link = $same_queries
            ? "$base_url/all/$status/$order/$filter"
            : "$base_url/all/$status/$order/$filter?query=" . $Search->search;

        ?>
          <a sub href="<?= $search_link;  ?>">
            <mbutton material filled=lighter <?php if ($same_queries) echo "active"; ?>>
              <p text bold><?= $Search->search; ?></p>
            </mbutton>
          </a>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</header>