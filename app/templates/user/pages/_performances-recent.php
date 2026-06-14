<?php

$Scores = $User->scores()
  ->with("beatmap")
  ->where("mode", $gumode)
  ->orderBy("play_time", "DESC")
  ->limit(18)
  ->get();

?>

<div style=margin-top:22em; content-width=wider fl fldircol gap=smol+>

  <div fl gap alic title-inline>
    <a href="<?= "/u/$User->id/$mode/$current_mod"; ?>">
      <mbutton mid outlined icon-only>
        <mi size=midler>arrow_back</mi>
      </mbutton>
    </a>
    <p text bold mid><?= __("Recently played") ?></p>
  </div>

  <div grid-repeat gap=smol>
    <?php

    foreach ($Scores ?? [] as $key => $Score)
      include TEMPLATE . "/score/_score.php";

    ?>
  </div>

</div>

<?php include TEMPLATE . "/user/_not_implemented.html"; ?>