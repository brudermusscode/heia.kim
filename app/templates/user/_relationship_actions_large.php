<?php

use Heiakim\Model\User;

/**
 * @var User $User
 */


$current_relationship_action ??= "follow";

?>

<div relationship-actions <?= $current_relationship_action; ?> fl jucc>
  <mbutton pn-option follow size=mid material icon-only background=slight-green color=dark-green data-action="relationships:create" data-user-id="<?= $User->id; ?>" has-tooltip=right>
    <mi>add</mi>
    <div ttooltip>
      <p text bold><?= __("Follow") ?></p>
    </div>
  </mbutton>

  <mbutton pn-option unfollow size=mid icon-only material background=slight-red color=dark-red data-action="relationships:remove" data-user-id="<?= $User->id; ?>" has-tooltip=right>
    <mi>hide_source</mi>
    <div ttooltip>
      <p text bold><?= __("Unfollow") ?></p>
    </div>
  </mbutton>

  <mbutton pn-option refollow animation=pulse size=mid icon-only material background=refollow color=dark-blue data-action="relationships:create" data-user-id="<?= $User->id; ?>" has-tooltip=right>
    <mi>sync_alt</mi>
    <div ttooltip>
      <p text bold><?= __("Follow back") ?></p>
    </div>
  </mbutton>
</div>