<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostVote;

class SquadPostVotesController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_interaction($Post);

    $this->params->type = (int) $this->params->type === -1 ? -1 : 1;

    /**
     * @var ?SquadPostVote
     */
    $Vote = $Post->votes()
      ->where("user_id", CurrentUser->id)
      ->first();

    # Vote with this specific type already exists?
    if ($Vote?->type === $this->params->type)
      return error("<strong>You have already voted, friend!</strong>");

    # If the CurrentUser has a vote already, they are changing their opinion and we
    # need to substract -1 from the old vote type count before we add a new.
    if ($Vote)
      $Post->update_feedback($Vote->type === 1 ? "upvotes" : "downvotes", -1);

    $this->params->type === 1
      ? $Post->upvote(by: CurrentUser)
      : $Post->downvote(by: CurrentUser);

    ob_start();
    $Post;
    include TEMPLATE . "/squad/post/_post.php";

    return success(data: [
      "Post" => $Post,
      "HTML" => ob_get_clean()
    ]);
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_interaction($Post);

    /**
     * @var ?SquadPostVote
     */
    $Vote = $Post->votes()
      ->where("user_id", CurrentUser->id)
      ->first() ?? die(error("No vote found!"));

    $Vote->delete();

    # Based on the vote type, we substract one from either the up- or downvotes.
    $Post->update_feedback($Vote->type === 1 ? "upvotes" : "downvotes", -1);

    ob_start();
    $Post;
    include TEMPLATE . "/squad/post/_post.php";

    return success(data: [
      "Post" => $Post,
      "HTML" => ob_get_clean()
    ]);
  }
}
