<?php

use Illuminate\Support\Collection;
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

if ($object_visibility) :

  /**
   * @var Collection<User>
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
        <p text bold ttup>Followers &nbsp;&middot;&nbsp; </p>
        <p text color=company><?= $Followers->count(); ?></p>
      </div>
      <?php if ($Followers->count()) { ?>
        <mbutton icon-only hoverable>
          <mi size=midler>call_received</mi>
        </mbutton>
      <?php } ?>
    </div>

    <?php if (!$Followers->count()) : ?>

      <box-model rounded=mid outlined pblock42 fl fldircol alic gap=smol mt=smol>
        <mbutton mid tag filled icon-only>
          <mi>call_received</mi>
        </mbutton>
        <p text bold midler tac>Nothing</p>
      </box-model>

    <?php else : ?>
      <div fl fldircol>
        <?php

        foreach ($Followers->take(5) as $Follower) :

          # Don't show the user if he is restricted.
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

        <?php endforeach; ?>

        <?php if ($User->followers->count() > 5) : ?>
          <mbutton smol filled=lighter has-icon=right dno>
            <p text smol bold>+<?= $Followers->count() - 5; ?></p>
            <mi>east</mi>
          </mbutton>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>