<?php

use Heiakim\Database\RedisManager;
use Heiakim\Registry\RedisRegistry;
use Heiakim\Time\Time;

?>

<div fl fldircol gap=smol>
  <div fl alic jucsb>
    <p text bold ttup>Dev Updates</p>
    <a extern target="_blank" href="https://github.com/brudermusscode/heia.kim">
      <mbutton icon-only hoverable>
        <mi color=yellow>deployed_code</mi>
      </mbutton>
    </a>
  </div>

  <div posrel fl fldircol gap=smoler>

    <?php

    $show = 6 - 1;
    $count = 0;
    $commits = new RedisManager()->connection()
      ->zRange(RedisRegistry::$github_commit_history, 0, $show, [
        "WITHSCORES" => true,
        "REV",
      ]);

    if (empty($commits)) : ?>
      <div p24 tac fl fldircol alic jucc outlined rounded slight>
        <p text bold>Huch?</p>
        <p text smol>Could not fetch history</p>
      </div>
    <?php else : ?>
      <div posabs mt12 style="height:calc(100% - 42px);width:3px;left:10.4px;top:0;" rounded background=slight></div>
    <?php endif; ?>

    <?php foreach ($commits ?? [] as $key => $unix_timestamp) :
      if ($count === $show + 1) break;
      $count++;
      $commit = json_decode($key, true);

    ?>
      <a extern target="_blank" href="<?= $commit["html_url"] ?? "#" ?>"
        fl alistart gap=smol>
        <mi mt4 style="height:24px;width:24px;" background=bg z posrel circled>commit</mi>
        <div flone outlined rounded pinline14 pblock8 clickable>
          <p text smolplus semibold>
            <?= $commit["commit"]["message"] ?? "Unknown message" ?></p>
          <div fl alic gap=smol>
            <p text smol mr4>
              <span color=company>
                <?= Time::ago($commit["commit"]["author"]["date"] ?? CURRENT_TIMESTAMP) ?></span>
            </p>
            &middot;
            <p text smol slight fl alic>
              <mi std>merge_type</mi> panties
            </p>
          </div>
        </div>
      </a>
    <?php endforeach; ?>
    <div fl alic gap=smol>
      <mi std mt4 style="height:24px;width:24px;" background=bg z posrel circled>open_in_new</mi>
      <a flone extern target="_blank" href="https://github.com/brudermusscode/heia.kim">
        <mbutton mt6 background=yellow color=Dark>
          Github Repository
        </mbutton>
      </a>
    </div>
  </div>
</div>