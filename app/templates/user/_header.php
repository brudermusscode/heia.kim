<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Squad;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub
 * @var string $more
 * @var string $current_mod
 * @var string $mode
 * @var string $mod
 * @var int $gumode
 * @var bool $is_champion
 * @var bool $is_my_profile
 * @var bool $has_played
 */

?>

<header full=user scroll-manipulated>
  <div user-new-inner>
    <picture cover>
      <?php $User->headline_cover($gumode); ?>
    </picture>

    <?php

    # + Only showing in Non Edit Mode.
    if (!IS_EDIT_MODE) : ?>
      <div privileges fl alic gap=smol w100 jucend hide-scrolled>
        <?php

        # Include dynamic role.
        include __DIR__ . "/_role.php"; ?>

        <?php if (!$is_my_profile && !in_array($User->id, [1, 2, 3, 4, 8])) { ?>
          <mbutton icon-only posrel background=unfollow color=dark-red has-tooltip=bottom
            request-get="report:new"
            data-type="user"
            data-id="<?= $User->id; ?>">
            <mi>campaign</mi>
            <div ttooltip>
              <p text bold>Report</p>
            </div>
          </mbutton>
        <?php } ?>
      </div>

      <picture image>
        <?php $User->image(); ?>
      </picture>
    <?php else : ?>
      <picture image update-profile-image clickable-zoom clickable>
        <div input-type=file fl jucc alic circled style="height:100%;width:100%;position:absolute;z-index:2;" background=hover hoverable>
          <mi wide color=invert fwb>add</mi>
          <input trigger=update-profile-image type="file" name="image" size="32" accept="image/*" hidden />
        </div>
        <?php $User->image(); ?>
      </picture>
    <?php endif; ?>

    <div bottom-wrap>
      <div>
        <p name text bold><?= $User->name(); ?></p>
        <div fl alic gap=smol+ hide-scrolled>
          <p text>
            Last active &middot;
            <span color=company>
              <?= Time::ago(date('Y-m-d h:i:s', $User->latest_activity)); ?></span>
          </p>
          <div style="height:1em;width:1px;" background=slight></div>
          <p text>Member since &middot;
            <span color=company><?= Time::ago($User->creation_time); ?></span>
          </p>
        </div>
      </div>

      <div ranking flexone fl alic gap=smol jucend>
        <?php

        if (!IS_EDIT_MODE) :
          include __DIR__ . "/_ranking.php";
        else : ?>

          <box-model background=bg rounded=wide>
            <bm-inr size=mid></bm-inr>
          </box-model>

        <?php endif; ?>
      </div>
    </div>
  </div>
</header>