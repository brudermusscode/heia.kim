<?php

namespace Bruder\Heiakim\Controller\Squad;

use Bruder\Controller;
use Bruder\Heiakim\Model\Squad\SquadPost;
use Bruder\Heiakim\Model\Squad\SquadPostComment;
use Bruder\Http\Request;

class SquadPostCommentsController extends Controller
{
  /**
   * CREATE -> POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["id", "comment_string"], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * User logged?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * User is socially excluded?
     */
    if ($this->CurrentUser->is_socially_excluded())
      return $this->error("!SOCIALLY_EXCLUDED");

    /**
     * Post exists?
     *
     * @var ?SquadPost
     */
    $Post = SquadPost::find($escaped_params->id);
    if (!$Post)
      return $this->error("<strong>This post doesn't exist.</strong>");

    /**
     * Append all.
     */
    $this->params->CurrentUser = $this->CurrentUser;
    $escaped_params->post = $Post;

    return (new SquadPostComment)->new($escaped_params);
  }

  /**
   * EDIT -> POST
   *
   * @param array $params
   * @return object
   */
  public function edit(array $params) {}

  /**
   * DELETE -> POST
   *
   * @param array $params
   * @return object
   */
  public function remove(array $params)
  {
    $escaped_params = $this->serialize_request_params(["id"], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * User logged?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Comment exists?
     *
     * @var ?SquadPostComment
     */
    $Comment = SquadPostComment::find($escaped_params->id);
    if (!$Comment)
      return $this->error("<strong>This doesn't exist.</strong>");

    /**
     * Append all.
     */
    $this->params->CurrentUser = $this->CurrentUser;

    return $Comment->remove($escaped_params);
  }

  /**
   * Serialize parameters.
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params([], $params, []);
  }
}
