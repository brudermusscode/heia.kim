<?php

use Heiakim\Model\Image;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var Squad $Squad
 * @var SquadFeedItem $Item
 */

/**
 * @var Image
 */
$Image = $Item->image;

?>

<div fl fldircol gap=smol>
  <div fl alic gap=smol>
    <mi slight>draw_abstract</mi>
    <p text bold slighter mid style=margin-top:-.2em;>&middot;</p>
    <p text slight>Squad</p>
  </div>

  <p text midler bold mb=smol>Updated the headline image</p>

  <picture w100 ovhid rounded=midler>
    <img vertalmid src="<?= CLAN_IMAGE_URL . "/headline-images/$Image->url"; ?>">
  </picture>
</div>