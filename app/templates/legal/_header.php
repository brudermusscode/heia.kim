<div class="legal__page_head" scroll-manipulated>
  <div class="legal__page_head__inr" fl alic jucsb>
    <div class=lphi_title fl alic>
      <a href="<?= $legal_head_url ?? $base_url; ?>">
        <mbutton material icon-only size=mid outlined=lighter clickable>
          <mi>west</mi>
        </mbutton>
      </a>
      <div>
        <p text std slogan><?= $legal_head_slogan; ?></p>
        <p text mid bold title><?= $legal_head_title; ?></p>
      </div>
    </div>
    <div mtooltip=bottom>
      <div outlined=lighter style=border-radius:50%;>
        <?php include TEMPLATE . "/components/ui/_theme_switcher.php"; ?>
      </div>
      <div ttooltip>
        <p text std bold><?= __("Switch color mode") ?></p>
      </div>
    </div>
  </div>
</div>