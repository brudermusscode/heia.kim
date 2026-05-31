<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;

class SearchesController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["type", "query"],
      optional: [],
    );

    $this->authorize();

    # Create a new Search.
    CurrentUser->searches()->create([
      "type" => $this->params->type,
      "search" => $this->params->query,
    ]);

    return success();
  }
}
