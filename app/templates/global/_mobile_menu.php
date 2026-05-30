<div class="mobile_menu" shadowed=min>
  <!--- SEARCH BUTTON --->
  <div search-icon>
    <mbutton elevated size=mid rounded icon-only material background=complement data-action="search:open" color=white>
      <i class=mi>search</i>
    </mbutton>
  </div>

  <div class="mobile_navigation">
    <div class=mn__inr>
      <a href="/home" <?php if (CURRENT_PAGE === "home" || !CURRENT_PAGE) echo "active"; ?>>
        <div class=option>
          <mi>browse</mi>
          <p text smol>Home</p>
        </div>
      </a>

      <a href="/leaderboard" <?php if (CURRENT_PAGE === "leaderboard") echo "active"; ?>>
        <div class=option>
          <mi>emoji_events</mi>
          <p text smol>Rankings</p>
        </div>
      </a>

      <a href="/beatmaps" <?php if (CURRENT_PAGE === "beatmaps" || CURRENT_PAGE === "beatmapsets") echo "active"; ?>>
        <div class=option>
          <mi>web_stories</mi>
          <p text smol>Beatmaps</p>
        </div>
      </a>

      <a href="/squads" <?php if (CURRENT_PAGE === "squads" || CURRENT_PAGE === "squad") echo "active"; ?>>
        <div class=option>
          <mi>workspaces</mi>
          <p text smol>Squads</p>
        </div>
      </a>

      <?php if (LOGGED) { ?>

        <a href="/u/<?= $CurrentUser->id; ?>" <?php if (CURRENT_PAGE === "u") echo "active"; ?>>
          <div class=option>
            <mi>face</mi>
            <p text smol><?= $CurrentUser->name; ?></p>
          </div>
        </a>

      <?php } else { ?>

        <a href="/login" <?php if (in_array(CURRENT_PAGE, ["login", "register"])) echo "active"; ?>>
          <div class=option>
            <mi>login</mi>
            <p text smol>Login</p>
          </div>
        </a>

      <?php } ?>
    </div>
  </div>
</div>