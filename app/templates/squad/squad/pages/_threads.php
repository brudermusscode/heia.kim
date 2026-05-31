<?php

use Heiakim\Model\Squad;

/**
 * @var Squad $Squad
 */

$UnpinnedThreads = $Squad->threads()
  ->with("posts.attachments")
  ->where("pinned", 0)
  ->orderBy("updated_at", "DESC")
  ->get();

$PinnedThreads = $Squad->threads()
  ->with("posts.attachments")
  ->where("pinned", 1)
  ->orderBy("updated_at", "DESC")
  ->get();

?>

<div class=squad content-width=widest fl fldircol gap=mid>
  <div fl jucsb gap=mid alic>
    <div style="width:20em;">&nbsp;</div>

    <div style="width:20em;">
      <div fl jucc>
        <mbutton material background=follow color=dark-green rounded=mid has-icon=left size=mid rounded=mid data-action="popup:open" data-href="/squad/thread/new">
          <i class=mi size=std>add</i>
          <p text std bold><?= __("Create new") ?></p>
        </mbutton>
      </div>
    </div>
  </div>

  <div fl jucsb gap alic>
    <!--- LEFT CONTENT --->
    <div style="width:20em;">&nbsp;</div>

    <!--- MAIN CONTENT --->
    <div fl fldircol gap=mid style=flex:1;>

      <?php if (!$Squad->threads->count()) { ?>
        <box-model rounded=wide filled=lighter p62 fl fldircol alic gap style=flex:1;>
          <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
            <i class="mi" size=wide>Gesture</i>
          </div>
          <div tac>
            <p text bold wide><?= __("No threads") ?></p>
            <p text std><?= __("Nobody has created a thread by now") ?></p>
          </div>
          <div fl justify-content=center>
            <mbutton data-action="popup:open" data-href="/squad/thread/new" material has-icon=left size=mid background=dynamic>
              <mi>add</mi>
              <p text bold><?= __("Create new") ?></p>
            </mbutton>
          </div>
        </box-model>
      <?php } else { ?>

        <?php if ($PinnedThreads->count()) { ?>
          <div fl fldircol gap=smol>
            <?php

            foreach ($PinnedThreads ?? [] as $Thread)
              include TEMPLATE . "/threads/_thread.php";

            ?>
          </div>
        <?php } ?>

        <div fl fldircol gap=smol>
          <?php

          foreach ($UnpinnedThreads ?? [] as $Thread)
            include TEMPLATE . "/threads/_thread.php";

          ?>
        </div>
      <?php } ?>

    </div>

    <!--- RIGHT CONTENT --->
    <div style="width:20em;"></div>
  </div>
</div>