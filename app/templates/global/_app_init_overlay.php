<overlay loading-app visible="true">
  <div fl fldircol alic jucc gap tac h100>
    <picture quadrat style="height:6em;width:6em;" animation=fade-in>
      <img src="<?= IMAGE . "/logo/painted/250.webp"; ?>" loaded=true>
    </picture>

    <?php if (CURRENT_PAGE == "connect") { ?>
      <p text midler bold>Preparing some matcha</p>
    <?php } else if (CURRENT_PAGE == "reconnect") { ?>
      <p text midler bold>Decanting the wine</p>
    <?php } ?>

    <div dynamic-color class="dot-container">
      <div class="dot-pulse"></div>
      <div class="dot-pulse"></div>
      <div class="dot-pulse"></div>
    </div>
  </div>
</overlay>