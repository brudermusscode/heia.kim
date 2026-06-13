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

if (!$is_my_profile) : ?>

  <div class="floating_container">
    <div tac style="max-width:600px;" fl fldircol gap=mid alic>
      <div fl fldircol gap alic>
        <div style=height:5.2em;width:5.2em; filled circled fl alic jucc>
          <mi size=wide>raven</mi>
        </div>
        <div fl fldircol gap=smolest>
          <p text bold wide><?= __("Restricted") ?></p>
          <p text std>
            <?= __("This user is currently in restricted mode and can not be accessed.") ?></p>
        </div>
      </div>
      <mbutton onclick="history.go(-1);" material size=mid has-icon=left outlined>
        <i class="mi">arrow_back</i>
        <p text std bold><?= __("Go back") ?></p>
      </mbutton>
    </div>
  </div>

<?php else : ?>

  <div class="floating_container">
    <div style="max-width:600px;" fl fldircol gap=mid alic>

      <div fl fldircol gap alic>
        <div style=height:5.2em;width:5.2em; filled circled fl alic jucc>
          <mi size=wide>raven</mi>
        </div>

        <div tac>
          <p text bold wide><?= __("Restricted") ?></p>
          <p text std><?= __("Your account is in restricted mode and can not be accessed.") ?></p>
        </div>

        <div fl fldircol gap=smoler>
          <a href="https://discord.gg/XNuFD25G7J" extern target="_blank">
            <div filled=darker p24 rounded clickable fl jucsb alic>
              <div fl gap>
                <mi size=midler>tips_and_updates</mi>
                <div fl gap align-items=center>
                  <p text std>
                    <?= __("Learn more about how to appeal and get your account unrestricted on our Discord.") ?>
                  </p>
                </div>
              </div>
              <mi size=midler>link</mi>
            </div>
          </a>

          <a href="/my/game/scores">
            <div filled=darker p24 rounded clickable fl jucsb alic>
              <div fl gap>
                <mi size=midler>overview_key</mi>
                <div fl gap align-items=center>
                  <p text std>
                    <?= __("Find an overview of all your scores for appealing in your user settings") ?>
                  </p>
                </div>
              </div>
              <mi size=midler>arrow_forward</mi>
            </div>
          </a>
        </div>
      </div>

      <div fl justify-content=center>
        <mbutton data-action="session:delete" material size=mid background=unfollow color=dark-red>
          <p text std bold><?= __("Logout") ?></p>
        </mbutton>
      </div>
    </div>
  </div>

<?php endif; ?>