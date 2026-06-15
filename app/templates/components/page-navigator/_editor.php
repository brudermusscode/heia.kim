<?php

use Heiakim\Model\Profile;
use Heiakim\Model\User;

/**
 * @var User
 */
$User = CurrentUser;

/**
 * @var Profile
 */
$Profile = CurrentUser->profile;

/**
 * @var object
 */
$DecodedProfile = CurrentUser->decoded_profile();

# Set the default wrapper to key = 0 as it represents the tabs in page navigator.
$wrapper = Profile::$wrapper[0];

?>

<page-navigator>
  <div></div>

  <?php

  /**
   * @var array
   */
  $tabs = $DecodedProfile->tabs_visibility;

  ?>

  <div pn-options>

    <mbutton mid pn-option icon-only background=slight-green color=dark-green has-tooltip=right disabled>
      <mi>add</mi>
      <div ttooltip>
        <p text bold><?= __("Follow") ?>: Can't be disabled</p>
      </div>
    </mbutton>

    <?php

    /**
     * @var bool
     */
    $show_tab = $Profile->bool_value($wrapper, "premium");

    ?>

    <mbutton mid pn-option icon-only has-tooltip=right <?php display_disabled_if(!$show_tab, "turned-off"); ?>>
      <div pn-o-button-off></div>
      <input type=hidden name="profile[<?= $wrapper; ?>][premium]" value=<?= $show_tab; ?> />
      <mi><?= PREMIUM_ICON; ?></mi>
      <div ttooltip>
        <p text bold>
          <?= !$User->is_premium() ? PREMIUM_NAME . " members can disable only" : PREMIUM_NAME; ?>
        </p>
      </div>
    </mbutton>

    <div pn-option pn-o-divider></div>

    <mbutton mid disabled pn-option icon-only background=clean has-tooltip=right>
      <mi>face</mi>
      <div ttooltip>
        <p text bold>Profile: Can't be disabled</p>
      </div>
    </mbutton>

    <?php

    /**
     * @var bool
     */
    $show_tab = $Profile->bool_value($wrapper, "statistics");

    ?>

    <mbutton mid pn-option icon-only background=clean has-tooltip=right <?php display_disabled_if(!$show_tab, "turned-off"); ?>>
      <div pn-o-button-off></div>
      <input type=hidden name="profile[<?= $wrapper; ?>][statistics]" value=<?= $show_tab; ?> />
      <mi>data_exploration</mi>
      <div ttooltip>
        <p text bold>Game Statistics</p>
      </div>
    </mbutton>

    <?php

    /**
     * @var bool
     */
    $show_tab = $Profile->bool_value($wrapper, "photos");

    ?>

    <mbutton mid pn-option icon-only background=clean has-tooltip=right <?php display_disabled_if(!$show_tab, "turned-off"); ?>>
      <div pn-o-button-off></div>
      <input type=hidden name="profile[<?= $wrapper; ?>][photos]" value=<?= $show_tab; ?> />
      <mi>photo_library</mi>
      <div ttooltip>
        <p text bold>Photos</p>
      </div>
    </mbutton>

    <div pn-option pn-o-divider></div>

    <mbutton mid disabled pn-option icon-only has-tooltip=right>
      <mi><?= EDITOR_ICON; ?></mi>
      <div ttooltip>
        <p text bold>Start Profile Editor: Can't be disabled</p>
      </div>
    </mbutton>
  </div>

  <a pn-option href="<?= $User->link(); ?>">
    <mbutton mid filled icon-only>
      <mi>arrow_back</mi>
    </mbutton>
  </a>
</page-navigator>