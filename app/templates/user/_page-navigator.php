<?php

use Heiakim\Model\Profile;
use Heiakim\Model\User;

/**
 * @var string $sub
 * @var string $mode
 * @var string $mod
 * @var string $current_mod
 * @var string $base_url
 */

/**
 * @var ?User
 */
$User = User::find($id ?? 0);

if (!$User) :
  include __DIR__ . "/_none.php";
else :

  $base_url_append = ($mode ? "/$mode" : "") . ($mode && $mod ? "/$mod" : "");
  $is_my_profile = $User->is(CurrentUser);
  $both_sides_can_interact_socially =
    !$User->is_socially_excluded() && !CurrentUser->is_socially_excluded();

  /**
   * @var Profile
   */
  $Profile = $User->profile;

  /**
   * @var object
   */
  $DecodedProfile = $User->decoded_profile();

?>

  <page-navigator>
    <div></div>

    <div pn-options>

      <?php

      # What action to show for the follow button.
      $current_relationship_action = CurrentUser->follows($User)
        ? "unfollow"
        : ($both_sides_can_interact_socially
          ? $User->follow_action_display(CurrentUser)
          : "hidden");

      # + Will only be shown if the CurrentUser is authenticated.
      if ($current_relationship_action !== "hidden") : ?>

        <?php

        # + Relationship actions.
        if (!$is_my_profile) : ?>
          <div relationship-actions <?= $current_relationship_action; ?> fl jucc>
            <mbutton mid
              data-action="relationship:create"
              data-user-id="<?= $User->id; ?>"
              pn-option follow icon-only background=slight-green color=dark-green has-tooltip=right>
              <mi>add</mi>
              <div ttooltip>
                <p text bold><?= __("Follow") ?></p>
              </div>
            </mbutton>

            <mbutton mid
              data-action="relationship:delete"
              data-user-id="<?= $User->id; ?>"
              pn-option unfollow icon-only background=slight-red color=dark-red has-tooltip=right>
              <mi>hide_source</mi>
              <div ttooltip>
                <p text bold><?= __("Unfollow") ?></p>
              </div>
            </mbutton>

            <mbutton mid
              data-action="relationship:create"
              data-user-id="<?= $User->id; ?>"
              pn-option refollow animation=pulse icon-only background=refollow color=dark-blue has-tooltip=right>
              <mi>sync_alt</mi>
              <div ttooltip>
                <p text bold><?= __("Follow back") ?></p>
              </div>
            </mbutton>
          </div>
        <?php endif; ?>

        <?php

        # + Premium button.
        if (
          $both_sides_can_interact_socially
          && $Profile->bool_value("tabs_visibility", "premium")
        ) : ?>
          <mbutton mid
            request-get="user:buy-premium"
            data-id="<?= $User->id ?>"
            pn-option icon-only has-tooltip=right
            <?= $User->is_premium()
              ? "background=premium color=premium"
              : "outlined"; ?>>
            <mi><?= PREMIUM_ICON; ?></mi>
            <div ttooltip>
              <p text bold>
                <?= $User->is_premium()
                  ? PREMIUM_NAME
                  : ($is_my_profile ? "Get " : "Gift ") . PREMIUM_NAME; ?>
              </p>
            </div>
          </mbutton>
        <?php endif; ?>

        <div pn-option pn-o-divider></div>
      <?php endif; ?>

      <a pn-option href="<?= "$base_url/overview" . $base_url_append ?>">
        <mbutton mid icon-only background=clean has-tooltip=right
          <?php display_active($sub, "overview") ?>>
          <mi>face</mi>
          <div ttooltip>
            <p text bold>Profile</p>
          </div>
        </mbutton>
      </a>

      <?php if ($Profile->bool_value("tabs_visibility", "statistics")) { ?>
        <a pn-option href="<?= "$base_url/statistics" . $base_url_append ?>">
          <mbutton mid icon-only background=clean has-tooltip=right
            <?php display_active($sub, "statistics") ?>>
            <mi>data_exploration</mi>
            <div ttooltip>
              <p text bold>Game Statistics</p>
            </div>
          </mbutton>
        </a>
      <?php } ?>

      <?php if ($Profile->bool_value("tabs_visibility", "photos")) { ?>
        <a pn-option href="<?= "$base_url/photos"; ?>">
          <mbutton mid icon-only background=clean has-tooltip=right
            <?php display_active($sub, "photos"); ?>>
            <mi>photo_library</mi>
            <div ttooltip>
              <p text bold>Photos</p>
            </div>
          </mbutton>
        </a>
      <?php } ?>

      <?php if ($is_my_profile) { ?>
        <div pn-option pn-o-divider></div>

        <a pn-option href="/editor">
          <mbutton mid icon-only has-tooltip=right>
            <mi><?= EDITOR_ICON; ?></mi>
            <div ttooltip>
              <p text bold>Start Profile Editor</p>
            </div>

            <div notification-dot></div>
          </mbutton>
        </a>
      <?php } ?>
    </div>

    <a pn-option href="/">
      <mbutton mid filled icon-only>
        <mi>arrow_back</mi>
      </mbutton>
    </a>
  </page-navigator>

<?php endif; ?>