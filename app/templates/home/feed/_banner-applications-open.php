<feed-section dialogue>
  <story-banner fl alic background=company color=light rounded=mid>
    <picture>
      <img src="<?= IMAGE . "/legal/apply.svg"; ?>" />
    </picture>

    <div fl gap fldircol>
      <div fl fldircol gap=smol>
        <p style="line-height:.9;" text wide bold>
          <?= __("Want to work with us?") ?></p>
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

    <mbutton close hoverable icon-only close-dialogue
      data-info-window="applications_open">
      <mi size=midler>close</mi>
    </mbutton>
  </story-banner>
</feed-section>