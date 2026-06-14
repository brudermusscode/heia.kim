<?php

$Beatmaps = $User->most_played_beatmaps($gumode, 18);

?>

<div style=margin-top:22em; content-width=wider fl fldircol gap=smol+>

  <div fl gap alic title-inline>
    <a href="<?= "/u/$User->id/$mode/$current_mod"; ?>">
      <mbutton mid outlined icon-only>
        <mi size=midler>arrow_back</mi>
      </mbutton>
    </a>
    <p text bold mid style=line-height:1.6em;>Beatmaps</p>
  </div>

  <div class="beatmaps" grid-repeat gap=smol clear-flex style=padding-top:0;>
    <?php

    foreach ($Beatmaps ?? [] as $key => $Beatmap) {
      echo "<div grid-keeper>";
      include COMPONENT . "/beatmaps/_beatmap.php";
      echo "</div>";
    }

    ?>
  </div>
</div>

<?php include TEMPLATE . "/user/_not_implemented.html"; ?>