<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var int $gumode
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($User->squad && $object_visibility) : ?>
  <div fl fldircol gap=smol>
    <div fl jucsb alic>
      <p text bold ttup>Squad</p>
      <?php if ($is_my_profile) : ?>
        <a href="<?= $User->squad_settings_link(); ?>">
          <mbutton icon-only hoverable>
            <mi size=midler>edit</mi>
          </mbutton>
        </a>
      <?php else : ?>
        <mbutton icon-only hoverable style="opacity:0;" tag>
          <mi size=midler>chess_king_2</mi>
        </mbutton>
      <?php endif; ?>
    </div>
    <?php include TEMPLATE . "/squad/_squad_small_outlined.php"; ?>
  </div>
<?php endif; ?>