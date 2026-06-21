<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadPost;

class SquadPostsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type", "comment_string"],
      optional: ["options", "enable_comments", "attachment_type", "attachment_id"],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    $Post = (new SquadPost)->new($this->params);

    ob_start();
    $Post;
    $is_new = true;
    include TEMPLATE . "/squad/post/_post.php";

    return success(
      "<strong>Posted!</strong> <a href=\"/squad/" . CurrentUser->squad->id . "#squad-post-$Post->id\">See it here &nbsp; <mi smol>open_in_new</mi>",
      data: [
        "Post" => $Post->withoutRelations(),
        "HTML" => ob_get_clean()
      ]
    );
  }

  /**
   * @return object
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["enable_comments"],
    );

    $this->authorize(
      resource: CurrentUser?->squad_user,
      respect_social_exclusion: true,
    );

    /**
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_touch($Post);

    $Post->edit($this->params);

    ob_start();
    $Post;
    include TEMPLATE . "/squad/post/_post.php";

    return success("Good!", data: [
      "Post" => $Post->withoutRelations(),
      "HTML" => ob_get_clean(),
    ]);
  }

  /**
   * @return string
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
     * @var ?SquadPost
     */
    $Post = SquadPost::findOrReturn($this->params->id);

    CurrentUser->sqauthorize_content_touch($Post);

    $Post->remove();

    return success("Deleted!", data: [
      "Post" => $Post->withoutRelations(),
    ]);
  }
}
