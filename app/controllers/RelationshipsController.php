<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\User;
use Heiakim\Model\Relationship;

class RelationshipsController extends Controller
{

  /**
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["user_id", "type"],
      optional: [],
    );

    $this->authorize(respect_social_exclusion: true);

    /**
     * @var ?User
     */
    $User = User::findOrReturn($this->params->user_id, "<strong>This user has left us!</strong>");

    # User wants to follow themselves?
    if (CurrentUser->is($User))
      return $this->error("<strong>Having a strong relationship to yourself is very important!</strong> We value this.");

    # CurrentUser does already follow the User?
    if (CurrentUser->follows($User))
      return $this->error("<strong>You are in love with this player already!</strong> I value your desires!");

    # Other user can interact with the community?
    if ($User->is_socially_excluded())
      return $this->error("!SOCIALLY_EXCLUDED_THIRD");

    # Add the new Following.
    CurrentUser->followings()
      ->attach($User, ["updated_at" => null]);

    # Send notification.
    $User->notifications()
      ->create([
        "type" => "__relationship__/follow",
        "reference_id" => CurrentUser->id,
        "updated_at" => null
      ]);

    return success("<strong>You are now following <a href='/u/" . $User->id . "'>&nbsp;" . $User->name . " &nbsp; <i class='ri-link-unlink'></i></a></strong>");
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

    $this->authorize(respect_social_exclusion: true);

    /**
     * @var ?User
     */
    $User = User::findOrReturn($this->params->user_id, "<strong>Oh, that player might have moved out!</strong>");

    # CurrentUser doesn't follow the User? This will also prevent the CurrentUser
    # from unfollowing themselves, as they have no option to follow themselves.
    if (!CurrentUser->follows($User))
      return $this->error("<strong>There is no relationship between you and this player.</strong>");

    # Remove relationship.
    CurrentUser->followings()->detach($User);

    return success(
      "<strong>I hope no tears will fall! </strong> Friendship ends here 😿",
      data: ["refollow" => $User->follows(CurrentUser)]
    );
  }
}
