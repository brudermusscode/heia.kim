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
      <div fl gap=smoler>
        <p text midler bold>Squad</p>
      </div>
      <?php if ($is_my_profile) { ?>
        <a href="<?= $User->squad_settings_link(); ?>">
          <mbutton icon-only mr=smol hoverable>
            <mi size=midler>edit</mi>
          </mbutton>
        </a>
      <?php } ?>
    </div>
    <?php include TEMPLATE . "/squad/_squad_small_outlined.php"; ?>
  </div>
<?php endif; ?>