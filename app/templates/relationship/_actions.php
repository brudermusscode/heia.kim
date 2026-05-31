<?php

use Heiakim\Model\User;

/**
 * @var ?User $User
 * @var ?User $Follower
 */

/**
 * @var User
 */
$ThisUser = $Follower ?? $User ?? CurrentUser;

/**
 * @var bool
 */
$is_me ??= $User->id === CurrentUser->id;

/**
 * @var bool
 */
$is_following ??= CurrentUser->follows($User);

?>

<?php if (!$is_me) { ?>
  <div relationship-actions <?= $is_following ? "unfollow" : "follow"; ?> z>
    <mbutton has-tooltip=bottom follow background=follow color=dark-green ripple-effect icon-only material
      data-action="relationships:create" data-user-id="<?= $ThisUser->id; ?>">
      <mi>add</mi>
      <div ttooltip>
        <p text std bold>Follow</p>
      </div>
    </mbutton>

    <mbutton has-tooltip=bottom unfollow ripple-effect background=unfollow color=dark-red icon-only material
      data-action="relationships:remove" data-user-id="<?= $ThisUser->id; ?>">
      <mi>hide_source</mi>
      <div ttooltip>
        <p text std bold>Unfollow</p>
      </div>
    </mbutton>

    <mbutton has-tooltip=bottom refollow material background=refollow color=dark-blue icon-only
      data-action="relationships:create" data-user-id="<?= $ThisUser->id; ?>">
      <i class="loader-pulse">
        <span></span>
      </i>
      <div ttooltip>
        <p text std bold>Follow back</p>
      </div>
    </mbutton>
  </div>
<?php } ?>