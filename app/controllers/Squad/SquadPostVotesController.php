<?php

namespace Bruder\Heiakim\Controller\Squad;

use Bruder\Controller;
use Bruder\Heiakim\Model\Squad\SquadPost;
use Bruder\Heiakim\Model\Squad\SquadPostVote;

class SquadPostVotesController extends Controller
{

  /**
   * POST
   *
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    $this->authorize(
      resource: $this->CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $this->params->SquadPost = SquadPost::findOrReturn($this->params->id, "<strong>Ney m8, post not found.</strong> Might have been deleted 🙃");

    /**
     * Authorize the user to interact with the SquadPost.
     */
    $this->CurrentUser->sqauthorize_content_interaction($this->params->SquadPost);

    return (new SquadPostVote)->new($this->params);
  }

  /**
   * UPDATE
   *
   * @return object
   */
  public function update() {}

  /**
   * DELETE
   *
   * @return object
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"],
      optional: [],
    );

    $this->authorize(
      resource: $this->CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPostVote
     */
    $Vote = $this->CurrentUser
      ->squad_post_votes()
      ->where("post_id", $this->params->id)
      ->first();

    /**
     * Vote exists?
     */
    if (!$Vote)
      return request_error("<strong>No vote foundii!</strong> 🤗");

    return $Vote->remove($this->params);
  }
}
