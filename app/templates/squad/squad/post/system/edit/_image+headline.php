<?php

use Heiakim\Model\Image;

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

  <div outlined ovhid rounded=midler>
    <picture style="padding-top:0%;width:100%;" posrel>
      <div background=hover style="z-index:2;position:absolute;top:0;left:0;height:100%;width:100%;">
      </div>
      <div style="position:absolute;top:0;left:0;height:100%;width:100%;">
        <img src="<?= CLAN_IMAGE_URL . "/headline-images/$Image->url"; ?>">
      </div>
      <div fl fldircol gap=smol p32 z>
        <picture size=wider circled>
          <?php $Squad->logo(); ?>
        </picture>
        <div fl alic gap=smol>
          <div tag filled="darker" pinline12 pblock6 rounded="wide" ttup>
            <p text midler bold><?= $Squad->tag; ?></p>
          </div>
          <p text wide bold color=light><?= $Squad->name; ?></p>
        </div>
      </div>
    </picture>
  </div>
</div>