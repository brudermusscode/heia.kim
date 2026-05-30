<?php

/**
 * @var string
 */
$country_abbreviation ??= "xx";

?>

<?php if ($country_abbreviation === "xx" || !$country_abbreviation) { ?>
  <mi color=special>circle</mi>
<?php } else { ?>
  <img style=vertical-align:top; src="<?= IMAGE . "/country-flags/$country_abbreviation.svg"; ?>" />
<?php } ?>