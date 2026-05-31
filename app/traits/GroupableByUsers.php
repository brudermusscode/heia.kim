<?php

namespace Heiakim\Trait;

trait GroupableByUsers
{
  /**
   * Groups a collection of eloquent models, that have a user
   * atatched to it by consecutive appearance of all users.
   *
   * @return array
   */
  public function group_by_users()
  {
    /**
     * @var array
     */
    $GroupedComments = [];

    /**
     * @var array
     */
    $current_group = [];

    /**
     * @var ?User
     */
    $LastUser = null;

    foreach ($this->items as $Object) {
      if (!$Object->user) continue;

      /**
       * Comment is from the same user, add it to
       * the current group.
       */
      if ($Object->user->is($LastUser)) {
        $current_group[] = $Object;

        /**
         * Comment is from another user, store the
         * current group and start a new one.
         */
      } else {
        if (!empty($current_group)) {
          $GroupedComments[] = $current_group;
        }

        $current_group = [$Object];
      }

      /**
       * Update LastUser
       */
      $LastUser = $Object->user;
    }

    /**
     * If there is a current group active, add it
     * to the grouped comments.
     */
    if (!empty($current_group)) {
      $GroupedComments[] = $current_group;
    }

    return $GroupedComments;
  }
}
