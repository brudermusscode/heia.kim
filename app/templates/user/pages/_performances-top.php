<?php

$Scores = $User->scores()
  ->with("beatmap")
  ->whereHas("beatmap", function ($q) {
    $q->where("status", 2);
  })
  ->where("status", 2)
  ->where("mode", $gumode)
  ->orderBy("pp", "DESC")
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
    <div fl fldircol alistart gap=smoler>
      <p text bold mid><?= __("Highest performances") ?></p>
      <p text smol bold filled=darker pinline12 pblock6 rounded=wide>🏅 Ranked</p>
    </div>
  </div>

  <div grid-repeat gap=smol>
    <?php

    foreach ($Scores ?? [] as $key => $Score)
      include TEMPLATE . "/score/_score.php";

    ?>
  </div>

</div>

<?php include TEMPLATE . "/user/_not_implemented.html"; ?>