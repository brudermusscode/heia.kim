<?php

use Heiakim\Database\Manager as DBM;
use Heiakim\Enum\Privilege;
use Heiakim\Model\User;
use Heiakim\Time\Time;

$db = new DBM;

$legal_head_slogan = __("Behind the scenes");
$legal_head_title  = __("The Team");

include TEMPLATE . "/legal/_header.php";

?>

<div class="legal__page_content__full" scroll-manipulated>
  <div class="legal__page_content__full_inr">
    <div class="inr__flex">
      <div flexone>
        <p text wider bold trimt><?= __("People") ?>,</p>
        <p text mid><?= __("that manage and build what you see") ?>.</p>
      </div>
      <div class="legal_image">
        <picture style=margin-bottom:-.9em;>
          <img src="<?= IMAGE . "/legal/team.svg"; ?>" />
        </picture>
      </div>
    </div>
  </div>
</div>


<div content-width=mid fl fldircol content-gap jucstretch>
  <div class="legal__page" first>
    <div class="legal__page_content">
      <box-model filled=lighter elevated>
        <bm-inr size=wide fl fldircol gap=smol+>
          <h2><?= __("It's our passion") ?></h2>
          <p text std><?= APP_NAME; ?> <?= __("is_build_with_passion") ?></p>
        </bm-inr>
        <div fl fldircol background=slighter rounded=min pblock62 pinline42>
          <p text std><strong><?= __("Interested in joining our team?") ?></strong></p>
          <p text std><?= __("You can apply through one of the forms on our {legal-applications-link}.") ?></p>
        </div>
      </box-model>
    </div>
  </div>

  <div class="timeline" style=width:100%;margin-bottom:0em>
    <div class="timeline__item">
      <div class="timeline__time">
        <div background=besure color=dark-orange class="timeline__time_circle">
          <mi wide>copyright</mi>
        </div>

        <div class="timeline__time_name">
          <p text bold mid color=white ttup><?= __("Owner's group") ?></p>
        </div>
      </div>

      <div class="timeline__items">
        <div class="timeline__items_outline" owner></div>

        <?php

        /**
         * @var User
         * Owner
         */
        $Users = User::whereIn("id", [3, 4])
          ->orderByDesc("priv")
          ->get();

        foreach ($Users as $User) {
          /**
           * @var User $User
           */

        ?>

          <a href="<?= $User->link(); ?>">
            <div class="timeline__items_option" has-tooltip clickable>
              <div class="timeline__items_option__inr">
                <div fl gap align-items=center>
                  <div class="image">
                    <picture circled>
                      <?php $User->image(); ?>
                    </picture>
                  </div>
                  <div class=actions>
                    <mbutton material size=mid filled=lighter icon-only>
                      <mi>arrow_forward</mi>
                    </mbutton>
                  </div>
                </div>
                <div class="name" mt=std>
                  <p text mid bold><?= $User->name(); ?></p>
                  <p text smol slight><?= __("Member since") ?> <?= Time::ago($User->created_at); ?></p>
                </div>
              </div>
            </div>
          </a>

        <?php } ?>

      </div>
    </div>
  </div>

  <?php

  foreach (Privilege::cases() as $key => $Privilege) {
    $privilege = $Privilege->get_display();

    /**
     * Skip these roles.
     */
    if (
      in_array(
        $Privilege,
        [
          Privilege::ALUMNI,
          Privilege::PREMIUM,
          Privilege::SUPPORTER,
          Privilege::VERIFIED,
          Privilege::MEMBER,
          Privilege::UNRESTRICTED,
        ]
      )
    )
      continue;

  ?>

    <div class="timeline">
      <div class="timeline__item">
        <div class="timeline__time">
          <div background=slight-green color=dark-green class="timeline__time_circle">
            <mi wide><?= $privilege->icon; ?></mi>
          </div>

          <div class="timeline__time_name">
            <p text bold mid color=white ttup>
              <?= ucwords(str_replace('_', ' ', $privilege->name)); ?>
            </p>
          </div>
        </div>

        <div class="timeline__items">
          <?php if ($key !== 0) echo '<div class="timeline__items_outline__top"></div>'; ?>
          <div class="timeline__items_outline">
            <div class="timeline__items_outline__latest">
              <p class=icon><i class="ri-open-arm-line"></i></p>
              <p text std color=white><?= __("Latest addition") ?> <strong></strong></p>
            </div>
          </div>

          <?php

          $users_in_privilege = [];

          /**
           * @var User
           * Owner
           */
          $Users = User::where("priv", ">", 3)
            ->whereNotIn("id", [3, 4])
            ->orderByDesc("priv")
            ->get();

          foreach ($Users as $User) {
            /**
             * @var User $User
             */

            /**
             * Check if any user has this privilege.
             */
            foreach ($User->privileges() as $user_privilege)
              if ($user_privilege->privilege == $Privilege)
                array_push($users_in_privilege, $User);
          }

          /**
           * Skip if no user has this privilege or display the
           * according users with a foreach.
           */
          if (!$users_in_privilege) {
            $discord_url = _env("DISCORD_INVITE");
            $text_for_none = __("No users in this role group");
            echo <<<TEXT
                <box-model filled=lighter>
                  <bm-inr size=wide>
                    <div fl fldircol gap=smol>
                      <p text mid>
                        <mi wide>$privilege->icon</mi>
                      </p>
                      <p text std>$text_for_none</p>
                    </div>
                  </bm-inr>
                </box-model>
              TEXT;
          } else
            foreach ($users_in_privilege as $User) {
              $u = $User->data();

          ?>

            <a href="<?= "/u/$User->id"; ?>">
              <div class="timeline__items_option" has-tooltip clickable>
                <div class="timeline__items_option__inr">
                  <div fl gap align-items=center>
                    <div class="image">
                      <picture circled>
                        <?php $User->image(); ?>
                      </picture>
                    </div>
                    <div class=actions>
                      <mbutton material size=mid filled=lighter icon-only>
                        <mi>arrow_forward</mi>
                      </mbutton>
                    </div>
                  </div>
                  <div class="name" mt=std>
                    <p text mid bold><?= $User->name(); ?></p>
                    <p text smol slight><?= __("Member since") ?> <?= Time::ago($User->created_at); ?></p>
                  </div>
                </div>
              </div>
            </a>

          <?php

            }

          ?>

        </div>
      </div>
    </div>

  <?php } ?>
</div>