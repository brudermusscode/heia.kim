<?php

use Bruder\Heiakim\Model\Thread\ThreadPost;
use Bruder\Time\Time;

/**
 * @var ThreadPost $Post
 */

$User = $Post->user;
$created_ago = Time::ago($Post->created_at);

/**
 * @var bool
 */
$is_new ??= false;

?>

<div fl fldircol gap=smol>
  <box-model filled=lighter rounded=wide <?= $is_new ? "new-object" : ""; ?>>
    <bm-inr size=std fl fldircol gap=smol+>
      <div fl alic gap=smol+ alic>
        <picture circled size=smol>
          <?php $User->image(); ?>
        </picture>
        <div fl fldircol gap=smoler>
          <div fl gap=smol alic>
            <p text std bold><?= $User->name(); ?></p>
            <p text std bold>&middot;</p>
            <p text std post-subcontent><?= $created_ago; ?></p>
          </div>
          <p text std post-content><?= htmlspecialchars_decode($Post->content); ?></p>
        </div>
      </div>
    </bm-inr>
  </box-model>

  <?php if ($Post->attachments->count()) { ?>
    <div fl fldircol gap=smoler>
      <?php

      foreach ($Post->attachments as $Attachment) {

        /**
         * @var Score
         */
        $Score = $Attachment->reference;
        $hide_reactions = true;

        include TEMPLATE . "/score/_score.php";
      }

      ?>
    </div>
  <?php } ?>
</div>