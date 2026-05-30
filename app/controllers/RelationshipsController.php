<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\User;
use Bruder\Heiakim\Model\Relationship;

class RelationshipsController extends Controller
{

  /**
   * POST
   *
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["user_id", "type"],
      optional: [],
    );

    /**
     * User logged in?
     */
    $this->authorize(respect_social_exclusion: true);

    /**
     * User wants to follow themselves?
     */
    if ($this->CurrentUser->id == $this->params->user_id)
      return $this->error("<strong>Having a strong relationship to yourself is very important!</strong> We value this.");

    /**
     * @var ?User
     */
    $User = User::findOrReturn($this->params->user_id, "<strong>This user has left us!</strong>");

    /**
     * Player doesn't follow?
     */
    if ($this->CurrentUser->follows($User))
      return $this->error("<strong>You are in love with this player already!</strong> I value your desires!");

    /**
     * Other user can interact with the community?
     */
    if ($User->is_socially_excluded())
      return $this->error("!SOCIALLY_EXCLUDED_THIRD");

    /**
     * Append all.
     */
    $this->params->User = $User;

    return (new Relationship)->new($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["user_id", "type"],
      optional: [],
    );

    /**
     * User logged in?
     */
    $this->authorize(respect_social_exclusion: true);

    /**
     * User tries to unfollow themselves?
     */
    if ($this->CurrentUser->id == $this->params->user_id)
      return $this->error("<strong>Having a strong relationship to yourself is very important!</strong> Do not give it up.");

    /**
     * Is following?
     */
    $User = User::findOrReturn($this->params->user_id, "<strong>Oh, that player might have moved out!</strong>");

    /**
     * Player doesn't follow?
     */
    if (!$this->CurrentUser->follows($User))
      return $this->error("<strong>There is no relationship between you and this player.</strong>");

    /**
     * Remove relationship.
     */
    $this->CurrentUser
      ->followings()
      ->detach($User);

    return $this->success(
      "<strong>I hope no tears will fall! </strong> Friendship ends here 😿",
      data: ["refollow" => $User->follows($this->CurrentUser)]
    );
  }
}
