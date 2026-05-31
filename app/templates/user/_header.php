<?php

use Heiakim\Time\Time;
use Heiakim\Model\User;

/**
 * @var ?User $User
 * @var object $rankings
 */

/**
 * Set the User tot the current user, if we are in editor mode.
 */
if (IS_EDIT_MODE) {
  $User = CurrentUser;
  $gumode = 0;
  $is_my_profile = true;
  $rankings = $User->get_rankings(0);
}

$rank_development = $rankings->development;

/**
 * Whether the user has played this gamemode already.
 */
$has_played = $rankings->global !== null;

/**
 * If the user is ranked #1, they get a champion badge displayed
 * on their profile.
 */
$is_champion = $rankings->global == 1;

/**
 * Status from bancho API
 */
// $bancho_status = $User->get_bancho_game_status();

// $is_online = $bancho_status->player_status->online ?? false;

/**
 * @var bool
 */
$both_sides_can_interact_socially = !$User->is_socially_excluded() && !CurrentUser->is_socially_excluded();

?>

<header full=user scroll-manipulated>
  <div user-new-inner>
    <picture cover>
      <?php $User->headline_cover($gumode); ?>
    </picture>

    <?php if (!IS_EDIT_MODE) { ?>
      <div privileges fl alic gap=smol w100 jucend hide-scrolled>
        <?php

        /**
         * Include dynamic role.
         */
        include __DIR__ . "/_role.php"; ?>

        <?php if (!$is_my_profile && !in_array($User->id, [1, 2, 3, 4, 8])) { ?>
          <mbutton data-action="popup:open" data-href="/report/new?type=user&id=<?= $User->id; ?>" material icon-only posrel background=unfollow color=dark-red has-tooltip=bottom>
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
    <?php } else { ?>
      <picture image update-profile-image clickable-zoom clickable>
        <div input-type=file fl jucc alic circled style="height:100%;width:100%;position:absolute;z-index:2;" background=hover hoverable>
          <mi wide color=invert fwb>add</mi>
          <input trigger=update-profile-image type="file" name="image" size="32" accept="image/*" hidden />
        </div>
        <?php $User->image(); ?>
      </picture>
    <?php } ?>

    <div bottom-wrap>
      <div>
        <p name text bold><?= $User->name(); ?></p>
        <div fl alic gap=smol+ hide-scrolled>
          <p text>
            Last active &middot; <span color=company><?= Time::ago(date('Y-m-d h:i:s', $User->latest_activity)); ?></span>
          </p>
          <div style="height:1em;width:1px;" background=slight></div>
          <p text>Member since &middot; <span color=company><?= Time::ago($User->creation_time); ?></span></p>
        </div>
      </div>

      <div ranking flexone fl alic gap=smol jucend>
        <?php

        if (!IS_EDIT_MODE)
          include __DIR__ . "/_ranking.php";
        else { ?>

          <box-model background=bg rounded=wide>
            <bm-inr size=mid></bm-inr>
          </box-model>

        <?php } ?>
      </div>
    </div>
  </div>
</header>




<?php if (!IS_EDIT_MODE) { ?>
  <page-navigator>
    <div></div>

    <div pn-options>

      <?php

      /**
       * @var string
       */
      $current_relationship_action = CurrentUser->follows($User)
        ? "unfollow"
        : ($both_sides_can_interact_socially
          ? $User->follow_action_display(CurrentUser)
          : "hidden");

      ?>

      <?php if ($current_relationship_action !== "hidden") { ?>

        <?php if (IS_EDIT_MODE || !$is_my_profile) { ?>
          <div relationship-actions <?= $current_relationship_action; ?> fl jucc>
            <mbutton
              data-action="relationship:create"
              data-user-id="<?= $User->id; ?>"
              pn-option follow size=mid material icon-only background=slight-green color=dark-green has-tooltip=right>
              <mi>add</mi>
              <div ttooltip>
                <p text bold><?= __("Follow") ?></p>
              </div>
            </mbutton>

            <mbutton
              data-action="relationship:delete"
              data-user-id="<?= $User->id; ?>"
              pn-option unfollow size=mid icon-only material background=slight-red color=dark-red has-tooltip=right>
              <mi>hide_source</mi>
              <div ttooltip>
                <p text bold><?= __("Unfollow") ?></p>
              </div>
            </mbutton>

            <mbutton
              data-action="relationship:create"
              data-user-id="<?= $User->id; ?>"
              pn-option refollow animation=pulse size=mid icon-only material background=refollow color=dark-blue has-tooltip=right>
              <mi>sync_alt</mi>
              <div ttooltip>
                <p text bold><?= __("Follow back") ?></p>
              </div>
            </mbutton>
          </div>
        <?php } ?>

        <?php

        /**
         * Premium+ button.
         */
        if ($both_sides_can_interact_socially && $Profile->bool_value("tabs_visibility", "premium")) { ?>

          <mbutton
            request-get="user:buy-premium"
            data-id="<?= $User->id ?>"
            pn-option material size=mid icon-only has-tooltip=right <?= $User->is_premium() ? "background=premium color=premium" : "outlined"; ?>>
            <mi><?= PREMIUM_ICON; ?></mi>
            <div ttooltip>
              <p text bold>
                <?= $User->is_premium() ? PREMIUM_NAME : ($is_my_profile ? "Get " : "Gift ") . PREMIUM_NAME; ?>
              </p>
            </div>
          </mbutton>

        <?php } ?>

        <div pn-option pn-o-divider></div>
      <?php } ?>

      <a pn-option href="<?= $base_url; ?>">
        <mbutton material icon-only size=mid background=clean has-tooltip=right <?= display_active($sub_page, "overview") ?>>
          <mi>face</mi>
          <div ttooltip>
            <p text bold>Profile</p>
          </div>
        </mbutton>
      </a>

      <?php if ($Profile->bool_value("tabs_visibility", "statistics")) { ?>
        <a disabled pn-option href="<?= "$base_url/$mode/$current_mod/statistics"; ?>">
          <mbutton material icon-only size=mid background=clean has-tooltip=right <?php display_active($sub_page, "statistics") ?>>
            <mi>data_exploration</mi>
            <div ttooltip>
              <p text bold>Game Statistics</p>
            </div>
          </mbutton>
        </a>
      <?php } ?>

      <?php if ($Profile->bool_value("tabs_visibility", "photos")) { ?>
        <a pn-option href="<?= "$base_url/photos"; ?>">
          <mbutton material icon-only size=mid background=clean has-tooltip=right <?php display_active($sub_page, "photos"); ?>>
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
          <mbutton size=mid material icon-only has-tooltip=right>
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
      <mbutton filled material icon-only size=mid>
        <mi>arrow_back</mi>
      </mbutton>
    </a>
  </page-navigator>

  <?php if (!$is_sub_page) { ?>
    <mode-menu>
      <jump-menu mm-menu filled="lighter" elevated color="dynamic">
        <div jm-inr>
          <a href="<?= "$base_url/osu/vanilla" . ($more ? "/$more" : ""); ?>">
            <div ripple-effect class="jm__option" hoverable>
              <mi class="osu-icon osu-vanilla"></mi>
              <p text><?= __("Standard"); ?></p>
              <?php if ($mode === "osu") { ?>
                <div class=jm__option_right_icon>
                  <mi color=company>done</mi>
                </div>
              <?php } ?>
            </div>
          </a>
          <a href="<?= "$base_url/taiko/vanilla" .
                      ($more ? "/$more" : ""); ?>">
            <div ripple-effect class="jm__option" hoverable>
              <mi class="osu-icon osu-taiko"></mi>
              <p text>Taiko</p>
              <?php if ($mode === "taiko") { ?>
                <div class=jm__option_right_icon>
                  <mi color=company>done</mi>
                </div>
              <?php } ?>
            </div>
          </a>
          <a href="<?= "$base_url/ctb/vanilla" . ($more ? "/$more" : ""); ?>">
            <div ripple-effect class="jm__option" hoverable>
              <mi class="osu-icon osu-ctb"></mi>
              <p text>Catch the Beat</p>
              <?php if ($mode === "ctb") { ?>
                <div class=jm__option_right_icon>
                  <mi color=company>done</mi>
                </div>
              <?php } ?>
            </div>
          </a>
          <a href="<?= "$base_url/mania/vanilla" .
                      ($more ? "/$more" : ""); ?>">
            <div ripple-effect class="jm__option" hoverable>
              <mi class="osu-icon osu-mania"></mi>
              <p text>Mania</p>
              <?php if ($mode === "mania") { ?>
                <div class=jm__option_right_icon>
                  <mi color=company>done</mi>
                </div>
              <?php } ?>
            </div>
          </a>
        </div>
      </jump-menu>

      <mbutton mm-open size=wide has-icon=left elevated=mid material background=company color=light>
        <div mm-open-loading>
          <?php include COMPONENT . "/dot-loader.html"; ?>
        </div>
        <mi class="osu-icon osu-<?= $mode === "osu" ? "vanilla" : $mode; ?>"></mi>
        <div>
          <p text bold>
            <?= ucfirst(
              $mode === "osu"
                ? __("Standard")
                : ($mode === "ctb"
                  ? "Catch the Beat"
                  : $mode)
            ); ?>
          </p>
        </div>
      </mbutton>
    </mode-menu>
  <?php } ?>
<?php } ?>

<!---
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,, Floating mobile actions ,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
--->

<?php

if (VERIFIED && !$User->is_restricted() && !$is_my_profile) {
  $current_relationship_action = $User->follow_action_display(CurrentUser);

?>
  <div floating-action>
    <?php include TEMPLATE . "/user/_relationship_actions_large.php"; ?>
  </div>
<?php } ?>