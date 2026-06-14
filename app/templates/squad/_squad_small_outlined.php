<?php

use Heiakim\Time\Time;
use Heiakim\Model\Squad;
use Heiakim\Enum\SquadPrivilege;

/**
 * @var Squad
 */
$Squad ??= $User->squad ?? CurrentUser->squad;

?>

<a href="<?= $Squad->link(); ?>">
  <box-model outlined=darker rounded=mid clickable p12>
    <div fl alic jucsb>
      <div fl alic gap=smol+>
        <?php

        /**
         * @var SquadPrivilege
         */
        $SquadRank = $User->squad_user->highest_privileges();

        ?>
        <picture size=midler circled posrel>
          <?php $Squad->logo(); ?>

          <div fl alic jucc z style="height:24px;width:24px;position:absolute;bottom:0;right:0;" circled background=invert color=invert>
            <mi smol><?= $SquadRank->icon ?></mi>
          </div>
        </picture>

        <div fl fldircol gap=smoler>
          <div fl alic gap=smoler>
            <div filled=darker rounded pinline6 pblock4 ttup>
              <p text smol bold color="dynamic"><?= $Squad->tag; ?></p>
            </div>
            <p text std bold><?= $Squad->name; ?></p>
          </div>
          <div fl alic gap=smoler>
            <div fl alic gap=smoler>
              <p text smol><?= $SquadRank->name ?></p>
            </div>
            &middot;
            <p text smol color=company><?= Time::ago($Squad->created_at, true); ?></p>
          </div>
        </div>
      </div>
    </div>
  </box-model>
</a>