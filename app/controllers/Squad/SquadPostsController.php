<?php

namespace Bruder\Heiakim\Controller\Squad;

use Bruder\Controller;
use Bruder\Heiakim\Model\Squad\SquadPost;

class SquadPostsController extends Controller
{

  /**
   * POST
   *
   * @return object
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type", "comment_string"],
      optional: ["options", "enable_comments", "attachment_type", "attachment_id"],
    );

    $this->authorize(
      resource: $this->CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * I don't need to check for the user being part of specific
     * squad here as the post will always be posted to the squad
     * the current user belongs to!
     */

    return (new SquadPost)->new($this->params);
  }

  /**
   * UPDATE
   *
   * @return object
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["enable_comments"],
    );

    $this->authorize(
      resource: $this->CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id, "<strong>Ney m8, post not found.</strong> Might have been deleted 🙃");

    /**
     * Authorize the user to touch this Post.
     */
    $this->CurrentUser->sqauthorize_content_touch($Post);

    return $Post->edit($this->params);
  }

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
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id, "<strong>Ney m8, post not found.</strong> Might have been deleted 🙃");

    /**
     * Authorize the user to touch this Post.
     */
    $this->CurrentUser->sqauthorize_content_touch($Post);

    return $Post->remove($this->params);
  }
}
