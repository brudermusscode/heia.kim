<?php

# A brief story.
if (!in_array("open_development", INFO_WINDOWS) && 1 === 2) { ?>
  <feed-section dialogue>
    <story-banner fl alic filled=darker rounded=mid>
      <picture style=width:24em;>
        <img src="<?= IMAGE . "/legal/dev.webp"; ?>" />
      </picture>

      <div fl gap fldircol>
        <div>
          <p text wide bold><?= __("A brief story") ?></p>
          <p text><?= __("Read along about how we approach development on {app-name}.") ?></p>
        </div>

        <div fl jucend>
          <a href="/legal/development">
            <mbutton background=invert color=invert>
              <p text bold><?= __("Learn more") ?></p>
            </mbutton>
          </a>
        </div>
      </div>

      <mbutton close filled=darker hoverable icon-only close-dialogue data-info-window="open_development">
        <mi size=midler>close</mi>
      </mbutton>
    </story-banner>
  </feed-section>
<?php } ?>