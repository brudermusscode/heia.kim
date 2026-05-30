<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Http\Request;
use Bruder\Controller;
use Bruder\Heiakim\Model\Comment;

class CommentsController extends Controller
{
  /**
   * POST
   *
   * @param array $params The params.
   * @return object Default return object.
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["id", "type", "comment_string"], $params, []);

    /**
     * User logged?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * User can interact with the community?
     */
    if ($this->CurrentUser->is_socially_excluded())
      return $this->error("!SOCIALLY_EXCLUDED");

    /**
     * Append current user to params object.
     */
    $escaped_params->CurrentUser = $this->CurrentUser;

    return (new Comment)->new($escaped_params);
  }

  /**
   * DELETE
   *
   * @param array $params The params
   * @return object
   */
  public function remove(array $params)
  {
    $escaped_params = $this->serialize_request_params(["id"], $params, []);;

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * User logged & verified?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * @var ?Comment
     */
    $Comment = Comment::find($escaped_params->id);
    if (!$Comment)
      return $this->error("<strong>This comment doesn't exist!</strong>");

    /**
     * Append all.
     */
    $escaped_params->CurrentUser = $this->CurrentUser;

    return $Comment->remove($escaped_params);
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params(["type", "comment_string", "reference_id"], $params, []);
  }
}
