<div fl fldircol gap=smol+>
  <div title-inline fl alic gap=smol+>
    <mbutton mid filled icon-only no-hover>
      <mi mid>favorite</mi>
    </mbutton>
    <p text mid bold>You might like</p>
  </div>

  <div grid-repeat gap=smol>
    <?php

    foreach ($Scores as $Score) {
      $Beatmap = $Score->beatmap;

      include TEMPLATE . "/components/beatmaps/_beatmap.php";
    }


    ?>
  </div>
</div>