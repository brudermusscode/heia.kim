<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\Thread\Thread;

/**
 * @var Thread $Thread
 */

/**
 * @var User
 */
$Creator = $Thread->user;

/**
 * @var string
 */
$created_ago = Time::ago($Thread->created_at);

?>

<a href="/squad/<?= $Squad->id; ?>/thread/<?= $Thread->id; ?>">
  <box-model filled rounded=wide clickable>

    <div style="position:absolute;top:1.2em;right:1.6em;" fl gap=smol>
      <?php if ($Thread->closed) { ?>
        <div has-tooltip=bottom>
          <i class="mi" size=std slight>lock</i>
          <div ttooltip>
            <p text std bold><?= __("Closed - Only owners can post") ?></p>
          </div>
        </div>
      <?php } ?>

      <?php if ($Thread->pinned) { ?>
        <div has-tooltip=bottom>
          <i class="mi" size=std slight>push_pin</i>
          <div ttooltip>
            <p text std bold><?= __("Pinned") ?></p>
          </div>
        </div>
      <?php } ?>
    </div>

    <bm-inr size=std fl gap alic jucsb>
      <div fl fldircol gap=smol+>
        <div fl gap=smol style=height:1.8em;>
          <div fl jucc alic gap=smol pblock12 background=slight rounded=wide>
            <i class="mi" size="smol+">maps_ugc</i>
            <p text std bold><?= number_format($Thread->posts->count()); ?></p>
          </div>
          <div fl jucc alic gap=smol pblock12 rounded=wide>
            <i class="mi" size="smol+">attach_file</i>
            <p text std bold><?= number_format($Thread->posts->flatMap->attachements->count()); ?></p>
          </div>
        </div>

        <p text mid bold><?= htmlspecialchars_decode($Thread->title); ?></p>

        <div fl gap style=height:1.8em;>
          <div fl alic gap=smol+>
            <picture circled size=smol>
              <?php $Creator->image(); ?>
            </picture>
            <div>
              <p text smol slight><strong><?= $Creator->name; ?></strong></p>
              <!-- <p text smol slight><?= $created_ago; ?></p> -->
            </div>
          </div>
        </div>
      </div>
      <i class="mi" size=std>arrow_forward</i>
    </bm-inr>
  </box-model>
</a>