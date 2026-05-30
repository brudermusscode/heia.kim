<div content-width=smol fl fldircol gap>
  <div fl gap align-items=center style="gap:1.8em;">
    <a href="/legal/<?= LEGAL_LANG; ?>">
      <mbutton outlined material icon-only size=mid color=light>
        <mi size=mid>arrow_back</mi>
      </mbutton>
    </a>
    <div>
      <p text mid bold color=white>Come with us!</p>
      <p text std color=white>Some important things to talk about</p>
    </div>
  </div>

  <?php

  $file_path = TEMPLATE . "/legal/consent/_$action.php";
  include file_exists($file_path) ? $file_path : TEMPLATE . "/legal/consent/_index.php";

  ?>
</div>