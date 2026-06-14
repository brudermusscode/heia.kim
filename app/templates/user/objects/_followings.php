<?php

use Heiakim\Enum\Privilege;
use Heiakim\Model\User;

/**
 * @var User $User
 * @var int $gumode
 * @var bool $is_my_profile
 */

/**
 * @var bool
 */
$object_visibility ??= 1;

if ($object_visibility) {

  /**
   * @var ?User
   */
  $Following = $User->followings()
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
        <p text bold ttup>Following &nbsp;&middot;&nbsp; </p>
        <p text color=company><?= $Following->count(); ?></p>
      </div>
      <?php if ($Following->count()) { ?>
        <mbutton icon-only mr=smol hoverable>
          <mi size=midler>call_made</mi>
        </mbutton>
      <?php } ?>
    </div>

    <?php if (!$Following->count()) { ?>

      <box-model rounded=mid outlined pblock42 fl fldircol alic gap=smol mt=smol>
        <mbutton mid tag filled icon-only>
          <mi>call_made</mi>
        </mbutton>
        <div tac>
          <p text bold midler>No one</p>

          <?php

          # Show a button to find new people if the viewing User is the User viewed.
          if (!$is_my_profile) : ?>
            <a disbl mt2 href="/leaderboard" normal>Find players to follow</a>
          <?php endif; ?>
        </div>
      </box-model>

    <?php } else { ?>
      <div fl fldircol>
        <?php foreach ($Following->take(5) as $Follower) {
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
          <mbutton smol filled=lighter has-icon=right dno>
            <p text smol bold>+<?= $Following->count() - 5; ?></p>
            <mi>east</mi>
          </mbutton>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
<?php } ?>