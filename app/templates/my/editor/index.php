<?php

use Heiakim\Model\Profile;
use Heiakim\Model\User;

redirect_unauthorized(CurrentUser);

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

/**
 * @var array
 */
$available_columns = array_keys(Profile::$sections_visibility);

$gumode = 0;

?>

<form data-form="profiles:edit" enctype="multipart/form-data">

  <div hidden submit-closest></div>

  <mbutton data-action="profiles:edit"
    wide ovhid editor-save has-icon="left" elevated="mid" background="green" color="light">
    <div inner-loading-hidden>
      <div dynamic-color class="dot-container">
        <div class="dot-pulse"></div>
        <div class="dot-pulse"></div>
        <div class="dot-pulse"></div>
      </div>
    </div>
    <mi>done_all</mi>
    <p text bold>Keep changes</p>
  </mbutton>

  <?php

  include __DIR__ . "/_page-navigator.php";
  include TEMPLATE . "/user/_header.php";

  /**
   * Set the default wrapper for all following objects.
   *
   * @var string
   */
  $wrapper = Profile::$wrapper[1];

  ?>


  <div page-structure="user">
    <div column-wrapper>

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


      <?php

      /**
       * @var string
       */
      $column_id = $available_columns[2];

      ?>

      <div editor-column=small editor-column-id="<?= $column_id; ?>"
        style="position:sticky;top:7.4em;width:20em;" fl fldircol gap=mid>

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