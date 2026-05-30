<?php

use Bruder\Time\Time;
use Bruder\Heiakim\Model\Beatmap;
use Bruder\Heiakim\Model\Beatmap\Set;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Comment;

/**
 * @var Score $Score
 */

/**
 * @var Beatmap
 */
$Beatmap = $Score->beatmap;

/**
 * @var Set
 */
$Set = $Beatmap?->set;

/**
 * @var User
 */
$User = $Score->user;

/**
 * @var int
 */
$show_comments = 2;
$more_comments_limit = 10;
$more_comments_offset = $show_comments;

/**
 * @var Comment
 */
$Comments = $Score
  ->squad_post_comments()
  ->orderBy("created_at", "DESC")
  ->get();

?>

<t-object fl fldircol gap=smoler post data-id=<?= $Score->id; ?>>
  <t-o-toolbar background=bg>
    <div fl alic gap=smol+>
      <t-o-icon>
        <a href="<?= $User->link(); ?>">
          <picture size=std circled clickable-zoom clickable>
            <?php $User->image(); ?>
          </picture>
        </a>
      </t-o-icon>

      <t-o-creator fl alic gap=smol>
        <a href="<?= $User->link(); ?>">
          <p text bold><?= $User->name(); ?></p>
        </a>
        <p text bold slight>&middot;</p>
        <p text color=company><?= Time::ago($Score->play_time, true); ?></p>
      </t-o-creator>
    </div>
  </t-o-toolbar>

  <div fl fldircol gap=smol>
    <?php

    /**
     * We can clean up the variables at the end of inclusion.
     */
    $cleanup_variables = false;

    include TEMPLATE . "/score/_score.php";

    ?>
  </div>

</t-object>