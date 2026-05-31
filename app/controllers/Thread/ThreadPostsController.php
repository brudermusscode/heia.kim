<?php

namespace Heiakim\Controller\Thread;

use Heiakim\Controller\Controller;
use Heiakim\Http\Request;
use Heiakim\Model\Thread\Thread;
use Heiakim\Model\Thread\ThreadPost;

class ThreadPostsController extends Controller
{
  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["id", "content"], $params, ["attachments"]);

    /**
     * User logged & verified?
     */
    if (!CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Valid params?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * @var ?Thread
     */
    $Thread = Thread::find($escaped_params->id);
    if (!$Thread)
      return $this->error();

    /**
     * User has squad and is the same as in the thread?
     */
    if (!CurrentUser->squad || CurrentUser->squad->id !== $Thread->squad->id)
      return $this->error();

    /**
     * Append current user.
     */
    $this->params->CurrentUser = CurrentUser;
    $escaped_params->thread = $Thread;

    return (new ThreadPost)->new($escaped_params);
  }

  /**
   * DELETE
   *
   * @param array $params
   * @return object
   */
  public function remove(array $params) {}

  /**
   * UPDATE
   *
   * @param array $params
   * @return object
   */
  public function edit(array $params) {}

  /**
   * Validate POST params
   *
   * @param array $params The params
   * @return object
   */
  public function sanitize_params(array $params)
  {
    return $this->serialize_request_params([], $params, []);
  }
}
