<div search class="search">
  <div class="search_outer">
    <div class="spacing">
      <div fl align-items=center gap=smol>
        <div class="search_outer__icon" flcenter>
          <p>
            <i class="ri-search-2-line"></i>
          </p>
        </div>

        <div class="search_outer__input" animation=fade-in>
          <input data-action="beatmaps:search" type-to-focus type="text"
            placeholder="<?= __("Search for your favorite beatmaps"); ?>" value="<?= $query; ?>" />
        </div>

        <div class="search_outer__icon" start flcenter>
          <p correct-top>
            <i class="ri-arrow-right-down-line"></i>
          </p>
        </div>
      </div>

      <div search-showcase></div>
    </div>

    <div search-loader class="linear-progress-material">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </div>
  </div>
</div>