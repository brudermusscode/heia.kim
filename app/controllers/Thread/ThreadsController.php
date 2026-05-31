<?php

namespace Heiakim\Controller\Thread;

use Heiakim\Controller\Controller;
use Heiakim\Model\Thread\Thread;

class ThreadsController extends Controller
{

  /**
   * POST
   *
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["title", "content"],
      optional: ["closed", "attachments"],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    return (new Thread)->new($this->params);
  }

  /**
   * DELETE
   *
   * @param array $params
   * @return object
   */
  public function delete(array $params) {}

  /**
   * UPDATE
   *
   * @param array $params
   * @return object
   */
  public function update(array $params) {}
}
