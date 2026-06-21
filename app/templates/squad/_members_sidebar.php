<div fl fldircol gap=smol+>
  <div fl jucsb alic gap pblock12>
    <p text bold mid><?= __("Members") ?></p>
  </div>
  <div fl fldircol gap=smoler>
    <?php

    foreach ($Members as $Member)
      include dirname(__DIR__) . "/_member.php";

    ?>
  </div>
</div>