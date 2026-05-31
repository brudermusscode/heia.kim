<?php

namespace Heiakim\Controller\Squad;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostPollAnswer;

class SquadPostPollAnswersController extends Controller
{
  /**
   * POST
   *
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "answer_key"],
      optional: [],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id, "<strong>Ney m8, post not found.</strong> Might have been deleted 🙃");

    /**
     * Authorize the user to interact with the SquadPost.
     */
    CurrentUser->sqauthorize_content_interaction($Post);

    return (new SquadPostPollAnswer)->new($this->params);
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
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPostPollAnswer
     */
    $PollAnswer = SquadPostPollAnswer::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_touch($PollAnswer);

    return $PollAnswer->remove($this->params);
  }
}
