<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub_page
 * @var string $more
 * @var string $current_mod
 * @var string $mode
 * @var string $mod
 * @var int $gumode
 * @var bool $is_champion
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var bool $both_sides_can_interact_socially
 */

$current_relationship_action ??= "follow";

?>

<div relationship-actions <?= $current_relationship_action; ?> fl jucc>
  <mbutton mid pn-option follow icon-only background=slight-green color=dark-green
    data-action="relationships:create"
    data-user-id="<?= $User->id; ?>" has-tooltip=right>
    <mi>add</mi>
    <div ttooltip>
      <p text bold><?= __("Follow") ?></p>
    </div>
  </mbutton>

  <mbutton mid pn-option unfollow icon-only background=slight-red color=dark-red
    data-action="relationships:remove"
    data-user-id="<?= $User->id; ?>" has-tooltip=right>
    <mi>hide_source</mi>
    <div ttooltip>
      <p text bold><?= __("Unfollow") ?></p>
    </div>
  </mbutton>

  <mbutton mid pn-option refollow animation=pulse icon-only background=refollow color=dark-blue
    data-action="relationships:create"
    data-user-id="<?= $User->id; ?>" has-tooltip=right>
    <mi>sync_alt</mi>
    <div ttooltip>
      <p text bold><?= __("Follow back") ?></p>
    </div>
  </mbutton>
</div>