<?php

use Heiakim\Time\Time;
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

<a href="/u/<?= $User->id; ?>">
  <box-model outlined=darker rounded=mid clickable>
    <bm-inr size=std>
      <div fl alic jucsb>
        <div fl alic gap=smol+>
          <picture size=midler circled>
            <?php $User->image(); ?>
          </picture>

          <div fl fldircol gap=smoler>
            <div fl alic gap=smol>
              <?php if ($User->squad) { ?>
                <div filled=darker rounded="wide" pinline8 pblock4 ttup>
                  <p text smol bold color="dynamic"><?= $User->squad->tag; ?></p>
                </div>
              <?php } ?>
              <picture size="smoler" icon-only circled has-tooltip="bottom" fl alic jucc>
                <?php $User->country_icon(); ?>
                <div ttooltip>
                  <p text bold><?= $User->country_string(); ?></p>
                </div>
              </picture>
              <p text std bold><?= $User->name(); ?></p>
            </div>
            <div fl alic gap=smoler>
              <p text smol>Last active</p>
              &middot;
              <p text smol color=company><?= Time::ago($User->latest_activity); ?></p>
            </div>
          </div>
        </div>
        <mbutton tag filled=lighter icon-only arrow-further=right
          style=right:1.2em;>
          <mi midler>east</mi>
        </mbutton>
      </div>
    </bm-inr>
  </box-model>
</a>