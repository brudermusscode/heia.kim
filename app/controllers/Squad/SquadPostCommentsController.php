<?php

namespace Heiakim\Controller\Squad;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadPost;
use Heiakim\Model\Squad\SquadPostComment;

class SquadPostCommentsController extends Controller
{

  /**
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "comment_string"]
    );

    /**
     * @var ?SquadPost
     */
    $this->params->Post = SquadPost::findOrReturn($this->params->id);

    # Authorize content touch.
    $this->can_interact(
      resource: CurrentUser?->squad_user,
      item: $this->params->Post,
    );

    return (new SquadPostComment)->new($this->params);
  }

  /**
   * @return object
   */
  public function edit() {}

  /**
   * @return object
   */
  public function remove()
  {

    $this->validate_params(
      strict: ["id"]
    );

    /**
     * @var ?SquadPostComment
     */
    $Comment = SquadPostComment::findOrReturn($this->params->id);

    # Authorize content touch.
    $this->can_interact(
      resource: CurrentUser?->squad_user,
      item: $Comment,
    );

    return $Comment->remove($this->params);
  }
}
