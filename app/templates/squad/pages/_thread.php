<?php

use Heiakim\Model\Thread\Thread;
use Heiakim\Model\Thread\ThreadPost;
use Heiakim\Model\Squad;
use Heiakim\Time\Time;
use Heiakim\Model\Beatmap\Set;

/**
 * @var Squad $Squad
 * @var Thread $Thread
 */

$thread_id = $sub;

/**
 * @var ?Thread
 */
$Thread = $Squad->threads()
  ->with("user")
  ->with("posts")
  ->find($thread_id);

if (!$Thread)
  include UNAVAILABLE;
else {

  /**
   * @var ThreadPost
   */
  $Posts = $Thread->posts->sortBy("created_at");

  /**
   * @var ThreadPost
   */
  $MainPost = $Posts->first();

  /**
   * @var User
   */
  $Creator = $MainPost->user;
  $created_ago = Time::ago($MainPost->created_at);

?>

  <div class=squad content-width=widest fl gap alistart>
    <div style="min-width:20em;">
      &nbsp;
    </div>

    <div threads style=flex:1; fl fldircol gap posrel>
      <div fl alic gap>
        <a href="<?= $base_url . "/threads"; ?>">
          <mbutton mid icon-only background=slighter>
            <i class="mi" size="std">arrow_back</i>
          </mbutton>
        </a>

        <p text mid bold pinline12><?= html_entity_decode($Thread->title); ?></p>
      </div>

      <div fl fldircol gap>
        <div fl fldircol gap=smol>
          <box-model outlined rounded=wide>
            <bm-inr size=mid fl fldircol gap=smol+>
              <div fl alic gap=smol+>
                <picture circled size=std>
                  <?php $Creator->image(); ?>
                </picture>
                <p text std bold><strong><?= $Creator->name(); ?></strong></p>
              </div>

              <p text std post-content style=font-size:17px;>
                <?= htmlspecialchars_decode($MainPost->content); ?>
              </p>

              <div fl gap=smol>
                <p text std post-subcontent><?= $created_ago; ?></p>
              </div>
            </bm-inr>
          </box-model>

          <div fl fldircol gap=smoler>
            <?php

            if ($MainPost->attachments->count()) {
              foreach ($MainPost->attachments as $Attachment) {

                switch ($Attachment->type) {
                  case "score":
                    /**
                     * @var Score
                     */
                    $Score = $Attachment->reference;
                    $hide_reactions = true;

                    include TEMPLATE . "/score/_score.php";
                    break;

                  case "beatmap":
                    /**
                     * @var Set
                     */
                    $Set = $Attachment->reference;

                    include TEMPLATE . "/beatmapset/_set-card.php";
                };
              }
            }

            ?>
          </div>
        </div>

        <?php

        /**
         * @var bool
         */
        $has_no_posts = $Posts->count() - 1 < 1;

        ?>

        <div fl fldircol gap=smol+>
          <div pblock24>
            <p text midler bold><span thread-post-count><?= $Thread->posts->count() - 1; ?></span>
              <?= __("posts") ?></p>
          </div>
          <div threads-container fl fldircol gap=smol <?php if ($has_no_posts) echo "empty-objects"; ?>>

            <box-model show-empty rounded="wide" filled="lighter" p62 fl fldircol alic gap>
              <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
                <i class="mi" size="wide">maps_ugc</i>
              </div>
              <div tac>
                <p text bold wide><?= __("Nothing") ?></p>
                <p text std><?= __("Nobody felt like answering this thread yet") ?></p>
              </div>
            </box-model>

            <?php

            /**
             * Iterate through all posts.
             */
            foreach ($Posts->skip(1) as $Post)
              include TEMPLATE . "/threads/_post.php";

            ?>
          </div>
        </div>
      </div>
    </div>

    <div style="min-width:20em;">
      <!-- <form data-form="threads:post,create,view"></form> -->
      <div fl jucc>
        <mbutton mid background=refollow color=dark rounded has-icon=left jucc fl alic
          data-action="get"
          data-href="/squad/thread/post/new?id=<?= $Thread->id; ?>"
          open-composer>
          <i class="mi" size="std">maps_ugc</i>
          <p text std bold><?= __("Answer") ?></p>
        </mbutton>
      </div>
    </div>
  </div>

<?php

}
