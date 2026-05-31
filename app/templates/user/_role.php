<?php

use Heiakim\Enum\Privilege;
use Heiakim\Model\User;

/**
 * @var User $User
 * @var bool $is_my_profile
 */

?>

<?php

/**
 * Owner.
 */
if ($User->id === 3) : ?>
  <a href="/legal/team">
    <mbutton ovhid material has-icon=left animation="premium" background=special color=light>
      <mi>keyboard</mi>
      <p text bold>Brudermusscode</p>
    </mbutton>
  </a>
<?php elseif ($User->id === 4) : ?>
  <a href="/legal/team">
    <mbutton ovhid material has-icon=left animation="premium" background=company color=company-text>
      <mi>taunt</mi>
      <p text bold>Owner's group</p>
    </mbutton>
  </a>
<?php

  /**
   * Aida.
   */
elseif ($User->id === 1) : ?>
  <mbutton tag material has-icon=left posrel background=blue color=light>
    <mi>smart_toy</mi>
    <p text bold>BOT</p>
  </mbutton>
  <?php

  /**
   * All others.
   */
else :

  /**
   * @var Privilege[]
   */
  $UserPrivileges = $User->privileges();

  /**
   * Unset Premium or supporter.
   */

  $counter = -1;
  foreach ($UserPrivileges as $Role) {
    $counter++;

    if (in_array($Role->privilege, [Privilege::PREMIUM, Privilege::SUPPORTER])) {
      unset($UserPrivileges[$counter]);
      continue;
    }
  }

  /**
   * Reset the array keys to start from 0 again.
   */
  $Roles = array_values($UserPrivileges);

  if ($UserPrivileges) {

    /**
     * @var object
     */
    $Role = $Roles[0];

    /**
     * @var string
     */
    $background = match ($Role->privilege) {
      Privilege::VERIFIED => "verified",
      default => "light",
    };

    /**
     * @var string
     */
    $color = match ($Role->privilege) {
      Privilege::VERIFIED => "verified",
      default => "dark",
    };

  ?>

    <mbutton tag material has-icon=left background=<?= $background; ?> color=<?= $color; ?>>
      <mi><?= $Role->icon; ?></mi>
      <p text bold><?= $Role->name; ?></p>
    </mbutton>

  <?php } ?>
<?php endif; ?>