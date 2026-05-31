<?php

use Heiakim\Model\Squad\SquadUser;

/**
 * @var string $tab
 */

/**
 * @var SquadUser
 */
$Members = $Squad->restricted_members();

?>

<div fl fldircol gap=mid>
  <?php

  $none = <<<HTML
      <box-model rounded=mid>
        <bm-inr size=wider fl fldircol gap=smol+ alic jucc>
          <div style="height:3.2em;width:3.2em;" filled=darker circled fl jucc alic>
            <mi>front_hand</mi>
          </div>
          <div tac>
            <p text midler bold>None</p>
            <p text>There are no restricted members</p>
          </div>
        </bm-inr>
      </box-model>
    HTML;

  ?>

  <div fl fldircol gap=smol>
    <p text bold midler title-inline>Restricted members</p>

    <?php

    if (!$Members->count())
      echo $none;
    else {
      foreach ($Members as $Member) {
        /**
         * @var User
         */
        $User = $Member->user;

        /**
         * Include the Member card.
         */
        include dirname(dirname(__DIR__)) . "/_member.php";
      }
    }

    ?>
  </div>
</div>