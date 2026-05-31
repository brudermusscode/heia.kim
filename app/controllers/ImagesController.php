<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Image;

class ImagesController extends Controller
{
  /**
   * CREATE -> POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params) {}

  /**
   * EDIT -> POST
   *
   * @param array $params
   * @return object
   */
  public function update(array $params) {}

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

    /**
     * User is logged?
     */
    $this->authorize();

    /**
     * @var ?Image
     */
    $Image = Image::findOrReturn($this->params->id, "<strong>Could not find that image!</strong>");

    CurrentUser->authorize_content_touch($Image);

    return $Image->remove($this->params);
  }
}
