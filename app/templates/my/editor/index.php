<?php

use Heiakim\Model\Profile;
use Heiakim\Model\User;

/**
 * @var bool IS_EDIT_MODE
 */

/**
 * @var Profile
 */
$Profile = CurrentUser->profile;

/**
 * @var object
 */
$DecodedProfile = CurrentUser->decoded_profile();

/**
 * @var array
 */
$available_columns = array_keys(Profile::$sections_visibility);

?>

<!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, PROFILE EDITOR ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

<form data-form="profiles:edit" enctype="multipart/form-data">

  <div hidden submit-closest></div>

  <mbutton wide data-action="profiles:edit" ovhid editor-save has-icon="left" elevated="mid" background="green" color="light">
    <div inner-loading-hidden>
      <div dynamic-color class="dot-container">
        <div class="dot-pulse"></div>
        <div class="dot-pulse"></div>
        <div class="dot-pulse"></div>
      </div>
    </div>
    <mi>done_all</mi>
    <div>
      <p text bold>Keep changes</p>
    </div>
  </mbutton>

  <?php

  /**
   * Header.
   */
  include TEMPLATE . "/user/_header.php";

  /**
   * Set the default wrapper for all following objects.
   *
   * @var string
   */
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

      <mbutton mid pn-option icon-only has-tooltip=right <?php display_disabled_if(!$User->is_premium()) ?>>
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



  <?php

  /**
   * Set the default wrapper for all following objects.
   *
   * @var string
   */
  $wrapper = Profile::$wrapper[1];

  ?>


  <div page-structure="user">
    <div column-wrapper>

      <!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, SIDEBAR ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

      <?php

      /**
       * @var string
       */
      $column_id = $available_columns[0];

      ?>

      <div editor-column=small editor-column-id=<?= $column_id; ?> style="position:sticky;top:7.4em;width:20em;" fl fldircol gap=mid>

        <?php

        /**
         * @var array
         */
        $Column = CurrentUser->decoded_profile()
          ->sections_visibility[0]
          ?? Profile::$sections_visibility[0];

        /**
         * Convention over configuration, huh? Thank you for this
         * point of view, Rails. I love you.
         */
        foreach ($Column as $object_name => $object_visibility) { ?>
          <div editor-object=droppable>
            <div fl fldircol gap=smol editor-object=draggable p12 rounded=wide background=bg>
              <?php $Profile->include_object_placeholder($object_name); ?>
            </div>
          </div>
        <?php } ?>

      </div>


      <!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, MIDDLE ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

      <?php

      /**
       * @var string
       */
      $column_id = $available_columns[1];

      ?>

      <div editor-column=large editor-column-id=<?= $column_id; ?> style=flex:1; fl fldircol gap=mid>

        <?php

        /**
         * @var array
         */
        $Column = CurrentUser->decoded_profile()
          ->sections_visibility[1]
          ?? Profile::$sections_visibility[1];

        /**
         * Convention over configuration, huh? Thank you for this
         * point of view, Rails. I love you.
         */
        foreach ($Column as $object_name => $object_visibility) { ?>
          <div editor-object=droppable>
            <div fl fldircol gap=smol+ editor-object=draggable p12 rounded=wide background=bg>
              <?php $Profile->include_object_placeholder($object_name); ?>
            </div>
          </div>
        <?php } ?>

      </div>


      <!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, SIDEBAR ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
  ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

      <?php

      /**
       * @var string
       */
      $column_id = $available_columns[2];

      ?>

      <div editor-column=small editor-column-id=<?= $column_id; ?> style="position:sticky;top:7.4em;width:20em;" fl fldircol gap=mid>

        <?php

        /**
         * @var array
         */
        $Column = CurrentUser->decoded_profile()
          ->sections_visibility[2]
          ?? Profile::$sections_visibility[2];

        /**
         * Convention over configuration, huh? Thank you for this
         * point of view, Rails. I love you.
         */
        foreach ($Column as $object_name => $object_visibility) { ?>
          <div editor-object=droppable>
            <div fl fldircol gap=smol editor-object=draggable p12 rounded=wide background=bg>
              <?php $Profile->include_object_placeholder($object_name); ?>
            </div>
          </div>
        <?php } ?>

      </div>
    </div>
  </div>
</form>