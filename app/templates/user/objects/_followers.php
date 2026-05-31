<?php

use Heiakim\Enum\Privilege;
use Heiakim\Model\User;

/**
 * @var User $User
 * @var int $gumode
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility) {

  /**
   * @var ?User
   */
  $Followers = $User->followers()
    ->whereRaw("(priv & ?) != 0", [Privilege::UNRESTRICTED->value])
    /** Only fetch public profiles. */
    ->whereHas("privacy", function ($q) {
      $q->where("is_public", 1);
    })
    ->get();

?>

  <div fl fldircol gap=smoler>
    <div fl jucsb alic>
      <div fl gap=smoler>
        <p text midler bold>Followers &middot; </p>
        <p text midler color=company><?= $Followers->count(); ?></p>
      </div>
      <?php if ($Followers->count()) { ?>
        <mbutton material icon-only mr=smol hoverable>
          <mi size=midler>call_received</mi>
        </mbutton>
      <?php } ?>
    </div>

    <?php if (!$Followers->count()) { ?>

      <box-model rounded=mid outlined p32 fl fldircol alic gap mt=smol>
        <div style="height:3.2em;width:3.2em;" fl alic jucc circled filled>
          <mi mid>call_received</mi>
        </div>
        <div tac>
          <p text bold midler>Nothing</p>
        </div>
      </box-model>

    <?php } else { ?>
      <div fl fldircol>
        <?php

        foreach ($Followers->take(5) as $Follower) {
          /**
           * Don't show the user if they are restricted.
           */
          if ($Follower->is_restricted()) continue;

        ?>

          <a href="<?= $Follower->link(); ?>">
            <div fl alic gap=smol+ hoverable p4 rounded=mid>
              <picture size=smol+ circled>
                <?php $Follower->image(); ?>
              </picture>
              <p text><?= $Follower->name(); ?></p>
            </div>
          </a>

        <?php } ?>

        <?php if ($User->followers->count() > 5) { ?>
          <mbutton material size=smol filled=lighter has-icon=right dno>
            <p text smol bold>+<?= $Followers->count() - 5; ?></p>
            <mi>east</mi>
          </mbutton>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
<?php } ?>