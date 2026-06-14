<?php

use Heiakim\Model\Feed;

/**
 * @var int
 */
$base_limit = 3;

/**
 * @var Feed
 */
$Feed = new Feed(CurrentUser);

?>

<?php if (!in_array("applications_open", INFO_WINDOWS)) { ?>
  <feed-section dialogue>
    <story-banner fl alic background=company color=light rounded=mid>
      <picture style=width:24em;margin-bottom:-2em;margin-top:-2em;>
        <img src="<?= IMAGE . "/legal/apply.svg"; ?>" />
      </picture>

      <div fl gap fldircol>
        <div fl fldircol gap=smol>
          <p style="line-height:.9;" text wide bold><?= __("Want to work with us?") ?></p>
          <p text>
            <?= __("Applications for <strong>Moderators</strong>, <strong>Assistants</strong> and <strong>Beatmap Nominators</strong> are open.") ?>
          </p>
        </div>

        <div fl jucend>
          <a href="/legal/applications">
            <mbutton background=invert color=invert>
              <p text bold><?= __("Apply now") ?></p>
            </mbutton>
          </a>
        </div>
      </div>

      <mbutton close filled=darker hoverable icon-only close-dialogue data-info-window="applications_open">
        <mi size=midler>close</mi>
      </mbutton>
    </story-banner>
  </feed-section>
<?php } ?>


<!--- NEW RANKED --->
<feed-section>
  <div class="feed_section__inr" fl fldircol gap>
    <div title-inline fl alic gap=smol+>
      <mbutton mid icon-only filled text midler>🏅</mbutton>
      <div fl fldircol>
        <h2><?= __("Newly ranked") ?></h2>
        <p text slight>Beatmaps that have recently been set to ranked state. Playing these will help you rank up.</p>
      </div>
    </div>

    <div class="feed_section__content" grid-repeat gap="smol">
      <get-content from="/home/get-content/newly-ranked">
        <?php include COMPONENT . "/animations/_loading_content_beatmaps.html"; ?>
      </get-content>
    </div>
  </div>
</feed-section>



<!--- NEW LOVED --->
<feed-section>
  <div class="feed_section__inr" fl fldircol gap>
    <div title-inline fl alic gap=smol+>
      <mbutton mid icon-only filled text midler>❤️</mbutton>
      <div fl fldircol>
        <h2><?= __("Newly loved") ?></h2>
        <p text slight>Beatmaps that are now featuring the loved state! Playing these won't help you rank up.</p>
      </div>
    </div>


    <div class="feed_section__content" grid-repeat gap="smol">
      <get-content from="/home/get-content/newly-loved">
        <?php include COMPONENT . "/animations/_loading_content_beatmaps.html"; ?>
      </get-content>
    </div>
  </div>
</feed-section>

<?php if (!in_array("open_development", INFO_WINDOWS)) { ?>
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


<!--- MOST PLAYED --->
<feed-section>
  <div class="feed_section__inr" fl fldircol gap>
    <div title-inline fl alic gap=smol+>
      <mbutton mid icon-only filled text midler>
        <mi midler>trending_up</mi>
      </mbutton>
      <div fl fldircol>
        <h2><?= __("Most played") ?></h2>
        <p text slight>Beatmaps, that have been played the very most over all by any player.</p>
      </div>
    </div>

    <div class="feed_section__content" grid-repeat gap="smol">
      <get-content from="/home/get-content/most-played-beatmaps">
        <?php include COMPONENT . "/animations/_loading_content_beatmaps.html"; ?>
      </get-content>
    </div>
  </div>
</feed-section>