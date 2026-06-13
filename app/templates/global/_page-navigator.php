<page-navigator>
  <div fl fldircol gap>
    <a href="/">
      <picture pn-option quadrat style="height:3.4em;width:3.4em;" loading>
        <img hover-zoom src="<?= IMAGE . '/logo/painted/100.webp'; ?>" />
      </picture>
    </a>

    <mbutton request-get="search"
      pn-option size=mid rounded icon-only material background=complement color=white>
      <i class=mi>explore</i>
    </mbutton>
  </div>

  <div pn-options>
    <a pn-option href="/home" page=home>
      <mbutton material icon-only size=mid background=clean hoverable has-tooltip=right <?= display_active_when_condition(!CURRENT_PAGE || CURRENT_PAGE === 'home') ?>>
        <mi>browse</mi>
        <div ttooltip>
          <p text bold><?= !LOGGED ? __("Beginning") : __("global.header.feed_text_1"); ?></p>
        </div>
      </mbutton>
    </a>

    <a pn-option href="/leaderboard" page=leaderboard>
      <mbutton material icon-only size=mid background=clean hoverable has-tooltip=right <?= display_active_when_condition(CURRENT_PAGE === "leaderboard") ?>>
        <mi>emoji_events</mi>
        <div ttooltip>
          <p text bold><?= __("Rankings") ?></p>
        </div>
      </mbutton>
    </a>

    <a pn-option href="/beatmaps" page=beatmaps>
      <mbutton material icon-only size=mid background=clean hoverable has-tooltip=right <?= display_active_when_condition(in_array(CURRENT_PAGE, ["beatmaps", "beatmap-set"])) ?>>
        <mi>web_stories</mi>
        <div ttooltip>
          <p text bold>Beatmaps</p>
        </div>
      </mbutton>
    </a>

    <a pn-option href="/squads" page=squads>
      <mbutton material icon-only size=mid background=clean hoverable has-tooltip=right <?= display_active_when_condition(in_array(CURRENT_PAGE, ["squads", "squad"])) ?>>
        <mi>workspaces</mi>
        <div ttooltip>
          <p text bold>Squads</p>
        </div>
      </mbutton>
    </a>
  </div>

  <div pn-options></div>
</page-navigator>