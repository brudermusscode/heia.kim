<?php

$Scores = $User->first_place_scores(
  gumode: $gumode,
  order: "pp",
  limit: 18,
);

?>

<div style=margin-top:22em; content-width=wider fl fldircol gap=smol+>

  <div fl gap alic title-inline>
    <a href="<?= "/u/$User->id/$mode/$current_mod"; ?>">
      <mbutton mid outlined icon-only>
        <mi size=midler>arrow_back</mi>
      </mbutton>
    </a>
    <div fl fldircol gap=smoler>
      <p text bold mid><?= __("First places") ?></p>
      <div fl gap=smoler>
        <p text smol bold filled pinline12 pblock6 rounded=min>🏅 Ranked</p>
        <p text smol bold filled pinline12 pblock6 rounded=min>❤️ Loved</p>
      </div>
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