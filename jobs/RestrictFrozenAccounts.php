<?php

// L::2024-12-15 21:39:01#

namespace Bruder\Heiakim\Job;

use Bruder\Job;
use Bruder\Heiakim\Enum\Privilege;
use Bruder\Heiakim\Model\User;
use Bruder\Time\Time;
use DateTime;

class RestrictFrozenAccounts extends Job
{
  /**
   * @var string
   */
  public $interval = "+5 days";

  /**
   * @return void
   */
  public function execute(?string $interval = null)
  {
    if (!Time::has_passed($this->last_executed(__FILE__), $interval ?? $this->interval))
      return;

    $this->update_last_executed(__FILE__);

    /**
     * @var ?User
     */
    $FrozenUsers = User::whereNotNull("frozen_at")
      ->get();

    /**
     * @var int
     */
    $count = 0;

    foreach ($FrozenUsers as $User) {
      /**
       * @var User $User
       */

      /**
       * User has sent a live play which is currently being reviewed?
       */
      if ($User->appeal_being_reviewed() || $User->is_restricted())
        continue;

      /**
       * Create a string, to display of how much time is left from the
       * premium+ subscription.
       */
      $freeze_ends = (new DateTime($User->frozen_at))
        ->modify($this->interval);
      $freeze_time_left = Time::left($freeze_ends->format("Y-m-d H:i:s"));

      /**
       * Delete premium feature.
       */
      if (!$freeze_time_left) {
        /**
         * Restrict the user.
         */
        $User->restrict((object) [
          "reason" => "Automatic restriction, freeze time passed.",
        ]);

        $count++;
      }
    }

    /**
     * Return.
     */
    if ($count)
      echo "\n" . date("d.m.Y<;>H:i:s") . "<;>Restricted $count frozen users!<&>\n";
  }
}
