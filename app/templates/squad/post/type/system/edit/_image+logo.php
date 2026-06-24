<?php

use Heiakim\Model\User;
use Heiakim\Model\Image;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var User $User
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

  <p text midler bold mb=smol>Updated the logo</p>

  <picture rounded=midler ovhid>
    <img vertalmid src="<?= CLAN_IMAGE_URL . "/logo-images/$Image->url"; ?>">
  </picture>
</div>