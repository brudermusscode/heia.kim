<?php

use Heiakim\Model\Squad;
use Heiakim\Time\Time;

/**
 * @var Squad $Squad
 */

?>

<header full=squad scroll-manipulated>
  <div inner>
    <div w100 fl jucend hide-scrolled posrel>
      <mbutton has-icon=left
        <?= $Squad->joinable === 0
          ? "background=unfollow color=dark-red"
          : (
            $Squad->joinable === 1
            ? "background=besure color=dark-orange"
            : "background=follow color=dark-green"
          ); ?>>
        <mi>
          <?= $Squad->joinable === 0
            ? "public_off"
            : (
              $Squad->joinable === 1 ? "vpn_lock" : "globe_asia"
            ); ?>
        </mi>
        <p text bold><?= $Squad->display_publicity(); ?></p>
      </mbutton>
    </div>

    <picture cover>
      <?php $Squad->headline(); ?>
    </picture>

    <picture image>
      <?php $Squad->logo(); ?>
    </picture>

    <div bottom-wrap>
      <div fl fldircol gap=smol>
        <div fl gap=smol+ alic tac>
          <p tag background=special color=light rounded=wide ttup>
            <?= $Squad->tag; ?></p>
          <p name text bold><?= $Squad->name; ?></p>
        </div>
        <p text hide-scrolled>Created &middot; <span color=company><?= Time::ago($Squad->created_at, true) ?></span></p>
      </div>
    </div>
  </div>
</header>