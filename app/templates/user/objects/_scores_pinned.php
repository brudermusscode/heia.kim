<?php

use Heiakim\Model\User;

/**
 * @var User $User
 * @var int $gumode
 * @var bool $has_played
 * @var int $fetch_limit
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility) {

  /**
   * @var ?UserPin
   */
  $ScorePins = $User->pins()
    ->where("type", "score")
    ->limit(6)
    ->get();

  if ($ScorePins->count()) {

?>

    <div fl fldircol gap=smol>
      <div fl fldircol gap=smol+>
        <div title-inline>
          <p text bold mid><?= __("Pinned") ?></p>
        </div>

        <div grid-repeat gap=smol pinned>

          <?php

          foreach ($ScorePins as $Pin) {
            $Score = $Pin->reference;
            include TEMPLATE . "/score/_score.php";
          }

          ?>
        </div>
      </div>
    </div>

  <?php } else { ?>

    <div grid-repeat gap=smol pinned style=margin-top:-2em;></div>

<?php
  }
}
?>