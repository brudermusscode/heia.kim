<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;
use Heiakim\Model\Stat;

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
    <div fl jucsb alic>
      <div fl gap="smoler">
        <p text midler bold>Ranking graph</p>
      </div>
    </div>
    <get-content mt=smol flexone from="/user/get-content/ranking-graph?id=<?= $User->id; ?>&gumode=<?= $gumode; ?>">
      <div style="width:100%;" fl jucc alic>
        <div dynamic-color class="dot-container">
          <div class="dot-pulse"></div>
          <div class="dot-pulse"></div>
          <div class="dot-pulse"></div>
        </div>
      </div>
    </get-content>
  </div>

<?php } ?>