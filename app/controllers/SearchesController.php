<?php

namespace Bruder\Heiakim\Controller;

use Bruder\Controller;

class SearchesController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type", "query"],
      optional: [],
    );

    /**
     * User logged in?
     */
    $this->authorize();

    /**
     * Create a new search!
     */
    $this->CurrentUser
      ->searches()
      ->create([
        "type" => $this->params->type,
        "search" => $this->params->query,
      ]);

    return $this->success();
  }
}
