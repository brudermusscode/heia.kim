<?php

/**
 * Return url based on being logged or not.
 */
$return_url = LOGGED ? "/u/" . CurrentUser->id : "/home";

?>

<header api-calls>
  <div class="api-calls__inr">
    <a href="<?= $return_url; ?>">
      <mbutton size=std background=slight has-icon material>
        <div fl align-items=center gap=smol>
          <p class="icon">
            <i color=white class="mi">arrow_back</i>
          </p>
          <p color=white text bold std><?= $api_header_back_button_text ?? "Go back"; ?></p>
        </div>
      </mbutton>
    </a>

    <div fl align-items=center gap>
      <div class="logo" fl align-items=center gap=smol>
      </div>
    </div>

    <div style="height:1.8em;width:1px;border-left:1px solid rgba(255,255,255,.24);"></div>

    <?php

    if (!in_array($api, ["google"]))
      if (isset($api_logo_url))
        echo <<< TEXT
          <div class=api_logo normalize-icon>
            <picture>
              <img src="$api_logo_url" loading=lazy />
            </picture>
          </div>
        TEXT;
      else
        echo <<<TEXT
        <div class=api_logo normalize-icon flex-truncate>
          <p text bold mid trimt>API request</p>
        </div>
      TEXT;

    else
      echo <<<TEXT
        <div>
          <p text wide color=white>
            $api_logo_url
          </p>
        </div>
      TEXT;

    ?>
  </div>
</header>