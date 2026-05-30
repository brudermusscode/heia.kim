<?php

use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Notification;

/**
 * @var Notification $Notification
 */

/**
 * @var User
 */
$User = $Notification->reference;

if (!$User) :
  include $unavailable;
else :

  /**
   * @var string
   */
  $birthday_year = date("Y", strtotime($Notification->created_at));

  /**
   * @var Feedback
   */
  $BirthdayCheers = $Notification->user->birthday_cheers(year: $birthday_year);

  /**
   * @var User
   */
  $LatestCheeringUser = $BirthdayCheers[0]->user;

?>

  <div notification class="notif__element">
    <form>
      <div class=notif__element_inr>
        <div class="notif__element_content" flex-truncate>
          <div fl gap alic mb=smol>
            <div circled class="notif__element_picture posrel" fl alic jucc>
              <p text mid>🎉</p>
              <?php if ($CurrentUser->has_birthday()) { ?>
                <div style="overflow: hidden;" align-center-screen>
                  <dotlottie-player src="https://lottie.host/c4710359-746c-42f2-8da3-7dc0c081ee52/YO8TI7K1dJ.json"
                    background="transparent" speed="1" style="width: 100px; height: 100px;" autoplay></dotlottie-player>
                </div>
              <?php } ?>
            </div>

            <div>
              <?php if ($BirthdayCheers->count() > 1) { ?>
                <p text std>
                  <a href="<?= $LatestCheeringUser->link(); ?>">
                    <strong><?= $LatestCheeringUser->name(); ?></strong>
                  </a>
                  and <strong><?= $BirthdayCheers->count() - 1; ?> others</strong> wish you all the best for your
                  birthday!
                </p>
              <?php } else { ?>
                <p text std>
                  <a href="<?= $LatestCheeringUser->link(); ?>">
                    <strong><?= $LatestCheeringUser->name(); ?></strong>
                  </a>
                  wishes you all the best for your birthday!
                </p>
              <?php } ?>
              <p text smol slight><?= $timestamp; ?></p>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

<?php endif;
