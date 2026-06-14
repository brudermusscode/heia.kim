<footer pblock48 color=white>
  <div class="page-end-logo" mt=wide mb=wide>
    <div page-end-bird justcontcent mt=wide>
      <picture size=mid quadrat id=logo-end>
        <img src="<?= IMAGE . "/logo/painted/100.webp"; ?>" />
      </picture>
    </div>
  </div>

  <div content-width=mid fl fldircol gap>
    <div flex-wrap=reverse fl alic jucsb gap>
      <div fl alic gap flex-wrap>
        <p text midler bold><?= APP_NAME; ?></p>
        <p text midler semi-bold disabled><?= __("Feedback") ?></p>
      </div>

      <div fl gap=smol flex-wrap=wrap alic>
        <a href="https://discord.gg/WYsnsUzYcR" extern target="_blank">
          <mbutton mid has-icon=left filled=lighter>
            <i class="ri-discord-fill"></i>
            <p text std bold>@heia.kim</p>
          </mbutton>
        </a>
        <a href="https://www.youtube.com/@heia.kim_osu" extern target="_blank">
          <mbutton mid has-icon=left filled=lighter>
            <i class="ri-youtube-fill"></i>
            <p text std bold>@heia.kim_osu</p>
          </mbutton>
        </a>
      </div>
    </div>

    <?php include TEMPLATE . "/global/_languages.php"; ?>
  </div>
</footer>