<?php

use Heiakim\Time\Time;
use Heiakim\Model\Session;

/**
 * @var Session $Session
 * @var string $category
 */

?>

<div open="security:device:<?= $Session->id ?>" rounded pinline12 pblock8 fl gap jucsb alic hoverable>
  <div fl gap=smol+ alic <?php if ($Session->deleted_at) echo "slight"; ?>>
    <div fl fldircol gap=smoler>
      <div fl alic gap=smolest>
        <p text bold><?= $Session->masked_ip() ?>.</p>
        <p pinline6 pblock8 background=slight rounded></p>
        <p pinline6 pblock8 background=slight rounded></p>
      </div>
      <div fl alic gap=smol>

        <?php

        # + Deleted session.
        if ($Session->deleted_at) : ?>
          <div fl gap=smol alic>
            <mi spec color=red>toggle_off</mi>
            <p text smol><?= __("Inactive") ?></p>
          </div>
          <p text smol slight>&middot;</p>
        <?php

        # + Current Session.
        elseif (
          $Session->token === $_COOKIE[Session::$persistent_cookies[1]]
        ) : ?>
          <div fl gap=smoler alic>
            <mi spec color=blue>check_circle</mi>
            <p text smol><?= __("Current") ?></p>
          </div>
          <p text smol slight>&middot;</p>
        <?php endif; ?>

        <p text smol color=company>
          <?= Time::ago($Session->updated_at ?? $Session->created_at, true); ?></p>
      </div>
    </div>
  </div>

  <mi midler>east</mi>
</div>