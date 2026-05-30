<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;
use Bruder\Heiakim\Model\Image;

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

    $this->CurrentUser->authorize_content_touch($Image);

    return $Image->remove($this->params);
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
