<?php

// L::2024-06-14 00:40:01#

namespace Bruder\Heiakim\Job;

use Bruder\Job;
use Bruder\Heiakim\Enum\Privilege;
use Bruder\Heiakim\Model\User;
use Bruder\Time\Time;

class RemovePremium extends Job
{

  /**
   * @return void
   */
  public function execute()
  {
    /**
     * @var ?User
     */
    $PremiumUsers = User::premium_members()
      ->get();

    /**
     * @var int
     */
    $count = 0;

    foreach ($PremiumUsers as $User) {
      /**
       * Create a string, to display of how much time is left from the
       * premium+ subscription.
       */
      $premium_time_left = Time::left($User->donor_end);

      /**
       * Delete premium feature.
       */
      if (!$premium_time_left) {
        /**
         * Remove discord role!
         */
        $User->discord?->take_role(PREMIUM_DISCORD_ROLE_ID);

        /**
         * Remove supporter privileges!
         */
        $User->remove_privileges(Privilege::SUPPORTER);

        /**
         * @var object
         */
        $DecodedProfile = $User->decoded_profile();

        /**
         * Create the default profile, if the user not yet has one.
         */
        if (!$DecodedProfile) $User->create_profile();
        else {

          /**
           * Set the premium tab to 1.
           */
          $DecodedProfile->tabs_visibility["premium"] = 1;

          /**
           * @var object
           */
          $params = (object) [
            "CurrentUser" => $User,
            "profile" => $DecodedProfile,
          ];

          /**
           * Update the profile.
           */
          $User->profile->edit($params);
        }

        /**
         * Increase the counter.
         */
        $count++;
      }
    }

    /**
     * ! Return
     */
    if ($count)
      echo "\n" . date("d.m.Y<;>H:i:s") . "<;>Removed $count Premium+ memberships!<&>\n";
  }
}
