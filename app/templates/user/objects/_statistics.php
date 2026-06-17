<?php

use Heiakim\Utils\Utils;
use Heiakim\Model\Stat;
use Heiakim\Model\Squad;
use Heiakim\Model\User;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var int $gumode
 * @var Stat $Stats
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility && $has_played) { ?>


  <div fl fldircol gap=smoler>
    <div fl alic jucsb>
      <p text bold ttup>Statistics</p>
      <a href="<?= $User->link() ?>/statistics">
        <mbutton icon-only hoverable color=special>
          <mi size="midler">stat_3</mi>
        </mbutton>
      </a>
    </div>
    <box-model mt=smol user-stats fl alic outlined rounded=mid>
      <bm-inr size=midler flexone>
        <div fl fldircol gap=smol>
          <div fl>
            <p text style="width:60%;">Ranked Score</p>
            <p text bold><?= Utils::round_with_ending($Stats->rscore); ?></p>
          </div>
          <div fl>
            <p text style="width:60%;">Hit Accuracy</p>
            <p text bold><?= number_format($Stats->acc, 2); ?> %</p>
          </div>
          <div fl>
            <p text style="width:60%;">Playcount</p>
            <p text bold><?= Utils::round_with_ending($Stats->plays); ?></p>
          </div>
          <div fl>
            <p text style="width:60%;">Total Score</p>
            <p text bold><?= Utils::round_with_ending($Stats->tscore); ?></p>
          </div>
          <div fl>
            <p text style="width:60%;">Total Hits</p>
            <p text bold><?= Utils::round_with_ending($Stats->total_hits); ?></p>
          </div>
          <div fl>
            <p text style="width:60%;">Highest Combo</p>
            <p text bold><?= Utils::round_with_ending($Stats->max_combo); ?></p>
          </div>
          <div fl>
            <p text style="width:60%;">Replay Views</p>
            <p text bold><?= Utils::round_with_ending($Stats->replay_views); ?></p>
          </div>
        </div>

        <div class=user_stats__inr fl fldircol gap=smol dno>

          <div class=option>
            <div class=option__inr>
              <p text bold><?= Utils::round_with_ending($Stats->rscore); ?></p>
            </div>
            <p class=option__desc>Ranked Score</p>
          </div>
          <div class=option>
            <div class=option__inr>
              <p text bold><?= Utils::round_with_ending($Stats->plays); ?></p>
            </div>
            <p class=option__desc><?= __("Plays") ?></p>
          </div>
          <div class=option>
            <div class=option__inr>
              <p text bold><?= number_format($Stats->max_combo); ?></p>
            </div>
            <p class=option__desc><?= __("Highest combo") ?></p>
          </div>


          <!--- Stats --->
          <div fl fldircol gap=smol mt=smol>
            <div title-inline dno>
              <p text bold mid>Rank development</p>
            </div>

            <div fl alic jucc gap=smol>
              <div fl fldircol gap=smoler alic>
                <div rounded=wide pblock12 pinline6 material tag score-grade="sh" color=white fl alic jucc gap=smol>
                  <p text bold>SS</p>
                </div>
                <p text smol bold><?= $Stats->xh_count; ?></p>
              </div>

              <div fl fldircol gap=smoler alic>
                <div rounded=wide pblock16 pinline6 material tag score-grade="sh" color=white fl alic jucc gap=smol>
                  <p text bold>S</p>
                </div>
                <p text smol bold><?= $Stats->x_count; ?></p>
              </div>

              <div fl fldircol gap=smoler alic>
                <div rounded=wide pblock12 pinline6 material tag score-grade="s" color=white fl alic jucc gap=smol>
                  <p text bold>SS</p>
                </div>
                <p text smol bold><?= $Stats->sh_count; ?></p>
              </div>

              <div fl fldircol gap=smoler alic>
                <div rounded=wide pblock16 pinline6 material tag score-grade="s" color=white fl alic jucc gap=smol>
                  <p text bold>S</p>
                </div>
                <p text smol bold><?= $Stats->s_count; ?></p>
              </div>

              <div fl fldircol gap=smoler alic>
                <div rounded=wide pblock16 pinline6 material tag score-grade="a" color=white fl alic jucc gap=smol>
                  <p text bold>A</p>
                </div>
                <p text smol bold><?= $Stats->a_count; ?></p>
              </div>
            </div>
          </div>

        </div>
      </bm-inr>
    </box-model>
  </div>

<?php } ?>