<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\Session;

/**
 * @var Session $Session
 */

?>

<div rounded pinline12 pblock8 fl gap jucsb alic hoverable
  data-category=<?= $category ?>
  data-sub=device
  data-id=<?= $Session->id ?>>
  <div fl gap=smol+ alic <?php if ($Session->deleted_at) echo "slight"; ?>>
    <mi mid class="<?= $Session->display()->icon_class; ?>"></mi>
    <div fl fldircol gap=smoler>
      <p text bold><?= $Session->display()->os_full; ?></p>
      <div fl alic gap=smol>
        <?php if ($Session->deleted_at) { ?>
          <div fl gap=smol alic>
            <mi spec color=red>toggle_off</mi>
            <p text smol><?= __("Inactive") ?></p>
          </div>
          <p text smol slight>&middot;</p>
        <?php } else if ($Session->token === $_COOKIE[Session::$persistent_cookies[1]]) { ?>
          <div fl gap=smol alic>
            <mi spec color=blue>check_circle</mi>
            <p text smol><?= __("Current") ?></p>
          </div>
          <p text smol slight>&middot;</p>
        <?php } ?>
        <p text smol slight>
          <?= $Session->city !== "None" ? $Session->city ?? __("Unknown city") : __("Unknown city"); ?></p>
        <p text smol slight>&middot;</p>
        <p text smol color=company>
          <?= Time::ago($Session->updated_at ?? $Session->created_at, true); ?></p>
      </div>
    </div>
  </div>

  <mi midler>east</mi>
</div>