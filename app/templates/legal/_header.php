<div class="legal__page_head" scroll-manipulated fl alic jucsb>
  <div style="max-width:1100px;" w100 minlineauto fl alic jucsb>
    <div fl alic gap>
      <a href="<?= $legal_head_url ?? $base_url; ?>">
        <mbutton midler background=dynamic color=dynamic clean icon-only>
          <mi>west</mi>
        </mbutton>
      </a>
      <p text midler bold><?= $legal_head_title; ?></p>
    </div>

    <div mtooltip=bottom>
      <?php include TEMPLATE . "/components/ui/_theme_switcher.php"; ?>
      <div ttooltip>
        <p text std bold><?= __("Switch color mode") ?></p>
      </div>
    </div>
  </div>
</div>