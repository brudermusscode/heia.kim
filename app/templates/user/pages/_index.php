<?php

use Heiakim\Model\User;
use Heiakim\Model\Stat;
use Heiakim\Model\Profile;

/**
 * @var User $User
 * @var Profile $Profile
 * @var Stat $Stats
 * @var bool IS_EDIT_MODE
 */

/**
 * @var object
 */
$DecodedProfile = $User->decoded_profile();

?>

<div page-structure=user>

  <!--- Left content --->
  <div top-actions>
    <?php if (CurrentUser->sqcan_invite($User)) { ?>
      <form request="squad:request:create" reload responder=always>
        <input type=hidden name=type value=invite />
        <input type=hidden name=user_id value=<?= $User->id; ?> />
        <mbutton submit-closest material has-icon=left size=mid background=slight>
          <mi>north_east</mi>
          <p text bold>Invite to <?= CurrentUser->squad->name; ?></p>
        </mbutton>
      </form>
    <?php } else

    if (
      ($Invitation = $User->has_invite_from(CurrentUser?->squad))
      && CurrentUser->sqcan("coordinate", "users")
    ) { ?>
      <a href="/manage/squad/requests?tab=invites">
        <mbutton submit-closest material has-icon=left size=mid background=slight>
          <mi class="loader-pulse posrel" fl alic jucc style="height:24px;width:24px;top:0;" mr=smol>
            <span></span>
          </mi>
          <p text bold>Invitation pending</p>
        </mbutton>
      </a>
    <?php } ?>
  </div>

  <div column-wrapper>
    <div column=small hide-mobile fl fldircol gap=mid>
      <?php

      /**
       * @var array
       */
      $ProfileFirstColumn = $DecodedProfile->sections_visibility[0]
        ?? $Profile->sections_visibility[0];

      /**
       * Convention over configuration, huh? Thank you for this
       * point of view, Rails. I love you.
       */
      foreach ($ProfileFirstColumn as $object_name => $object_visibility) {
        $object_file_path = dirname(__DIR__) . "/objects/_$object_name.php";

        /**
         * Include the object file, if it exists.
         */
        if (file_exists($object_file_path))
          include $object_file_path;
      }

      ?>
    </div>

    <!--- Mid content --->
    <div column=large flexone fl fldircol gap=mid>
      <?php

      $fetch_limit = 7;

      /**
       * @var array
       */
      $ProfileFirstColumn = $DecodedProfile->sections_visibility[1]
        ?? $Profile->sections_visibility[1];

      # Convention over configuration, huh? Thank you for this point of view, Rails.
      # I love you 🙂
      foreach ($ProfileFirstColumn as $object_name => $object_visibility) {
        $object_file_path = dirname(__DIR__) . "/objects/_$object_name.php";

        if (file_exists($object_file_path))
          include $object_file_path;
      }

      ?>
    </div>

    <!--- Right content --->
    <div column=small hide-tablet fl fldircol gap=mid>

      <?php

      /**
       * Birthday cheering button!
       */
      if ($User->has_birthday()) {

        /**
         * @var bool
         */
        $have_cheered = CurrentUser->cheered_for_birthday($User, date("y"));

      ?>
        <box-model background=invert rounded=wide <?php if (!$have_cheered) echo "elevated=wide"; ?> style="background:url(<?= IMAGE . "/birthday-card.jpg"; ?>) center center no-repeat;background-size:cover;">
          <bm-inr size=std>
            <div background=invert p24 fl fldircol gap rounded=wide>
              <p text tac color=invert><strong>It's my birthday!</strong></p>
              <div fl jucc>
                <?php if (!$have_cheered) { ?>
                  <div fl fldircol gap=smol>
                    <div fl alic jucc posrel append-animation>
                      <form data-form="feedback:birthday,create">
                        <input type=hidden name=reference_id value=<?= $User->id; ?> />
                        <input type=hidden name=action value=thumb_up />
                        <input type=hidden name=type value=birthday_cheer />
                        <mbutton submit-closest size=mid background=dynamic material has-icon=left>
                          <p text mid>🎉</p>
                          <p counter text bold><?= $User->birthday_cheers(year: date("Y"))->count(); ?>
                          </p>
                        </mbutton>
                      </form>
                    </div>
                    <p text color=invert smol tac slight>Send some wishes</p>
                  </div>
                <?php } else { ?>
                  <mbutton disabled size=mid background=dynamic material has-icon=left>
                    <p text mid>🎉</p>
                    <p counter text bold><?= $User->birthday_cheers(year: date("Y"))->count(); ?></p>
                  </mbutton>
                <?php } ?>
              </div>
            </div>
          </bm-inr>
        </box-model>
      <?php } ?>

      <?php

      /**
       * @var array
       */
      $ProfileFirstColumn = $DecodedProfile->sections_visibility[2]
        ?? $Profile->sections_visibility[2];

      /**
       * Convention over configuration, huh? Thank you for this
       * point of view, Rails. I love you.
       */
      foreach ($ProfileFirstColumn as $object_name => $object_visibility) {
        $object_file_path = dirname(__DIR__) . "/objects/_$object_name.php";

        /**
         * Include the object file, if it exists.
         */
        if (file_exists($object_file_path))
          include $object_file_path;
      }

      ?>
    </div>
  </div>
</div>