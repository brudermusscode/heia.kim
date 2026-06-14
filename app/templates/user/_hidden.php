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

?>

<div class="floating_container">
  <div tac style="max-width:600px;" fl fldircol gap=mid alic>
    <div fl fldircol gap alic>
      <div style=height:5.2em;width:5.2em; filled circled fl alic jucc>
        <mi size=wide>disabled_visible</mi>
      </div>
      <div fl fldircol gap=smolest>
        <p text bold wide><?= __("Private profile") ?></p>
        <p text std>
          <strong><?= $User->name; ?></strong>
          <?= __("has turned off the visibility of their profile.") ?>
        </p>
      </div>
    </div>

    <mbutton mid onclick="history.go(-1);" has-icon=left outlined>
      <i class="mi">arrow_back</i>
      <p text std bold><?= __("Go back") ?></p>
    </mbutton>
  </div>
</div>