<?php

use Heiakim\Model\User;
use Heiakim\Model\Squad;

/**
 * @var User $User
 * @var ?Squad $Squad
 * @var object $rankings
 * @var object $rank_development
 * @var string $base_url
 * @var string $sub_page
 * @var string $more
 * @var string $current_mod
 * @var string $mode
 * @var string $mod
 * @var int $gumode
 * @var bool $is_champion
 * @var bool $is_my_profile
 * @var bool $has_played
 * @var bool $both_sides_can_interact_socially
 */

/**
 * @var object
 */
$DecodedProfile = $User->decoded_profile();

?>

<div page-structure=user>

  <!--- Left content --->
  <div top-actions>
    <?php

    # + CurrentUser can invite this User.
    if (CurrentUser->sqcan_invite($User)) : ?>
      <form request="squad:request:create" reload responder=always>
        <input type=hidden name=type value=invite />
        <input type=hidden name=user_id value=<?= $User->id; ?> />
        <mbutton mid submit-closest has-icon=left background=slight>
          <mi>north_east</mi>
          <p text bold>Invite to <?= CurrentUser->squad->name; ?></p>
        </mbutton>
      </form>
      <?php else :

      # + If there is an invitation pending.
      if (
        ($Invitation = $User->has_invite_from(CurrentUser?->squad))
        && CurrentUser->sqcan("coordinate", "users")
      ) : ?>
        <a href="/manage/squad/requests?tab=invites">
          <mbutton mid submit-closest has-icon=left background=slight>
            <mi class="loader-pulse posrel" fl alic jucc style="height:24px;width:24px;top:0;" mr=smol>
              <span></span>
            </mi>
            <p text bold>Invitation pending</p>
          </mbutton>
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div column-wrapper>
    <div column=smaller hide-mobile fl fldircol gap=mid>
      <?php

      /**
       * @var array
       */
      $ProfileFirstColumn = $DecodedProfile->sections_visibility[0]
        ?? $Profile->sections_visibility[0];

      # Convention over configuration, huh? Thank you for this point of view, Rails.
      # I love you.
      foreach ($ProfileFirstColumn as $object_name => $object_visibility) :
        $object_file_path = dirname(__DIR__) . "/objects/_$object_name.php";

        # + Include the object file, if it exists.
        if (file_exists($object_file_path))
          include $object_file_path;
      endforeach;

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

      # Birthday cheering button!
      if ($User->has_birthday()) :

        /**
         * @var bool
         */
        $have_cheered = CurrentUser->cheered_for_birthday($User, date("y"));

      ?>
        <box-model background=invert rounded=wide
          <?php if (!$have_cheered) echo "elevated=wide"; ?>
          style="background:url(<?= IMAGE . "/birthday-card.jpg"; ?>) center center no-repeat;background-size:cover;">
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
                        <mbutton mid submit-closest background=dynamic has-icon=left>
                          <p text mid>🎉</p>
                          <p counter text bold><?= $User->birthday_cheers(year: date("Y"))->count(); ?>
                          </p>
                        </mbutton>
                      </form>
                    </div>
                    <p text color=invert smol tac slight>Send some wishes</p>
                  </div>
                <?php } else { ?>
                  <mbutton mid disabled background=dynamic has-icon=left>
                    <p text mid>🎉</p>
                    <p counter text bold><?= $User->birthday_cheers(year: date("Y"))->count(); ?></p>
                  </mbutton>
                <?php } ?>
              </div>
            </div>
          </bm-inr>
        </box-model>
      <?php endif; ?>

      <?php

      /**
       * @var array
       */
      $ProfileFirstColumn = $DecodedProfile->sections_visibility[2]
        ?? $Profile->sections_visibility[2];

      foreach ($ProfileFirstColumn as $object_name => $object_visibility) :
        $object_file_path = dirname(__DIR__) . "/objects/_$object_name.php";

        # Include the object file, if it exists.
        if (file_exists($object_file_path))
          include $object_file_path;
      endforeach; ?>
    </div>
  </div>
</div>