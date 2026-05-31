<?php

namespace Heiakim\Controller\Squad;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadFeedItem;

class SquadFeedItemsController extends Controller
{

  /**
   * @return object
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"]
    );

    /**
     * @var ?SquadFeedItem
     */
    $Item = SquadFeedItem::findOrReturn(
      $this->params->id,
      "<strong>This feed item doesn't exist!</strong> It might have been deleted."
    );

    $this->can_interact(
      resource: CurrentUser?->squad_user,
      item: $Item,
    );

    # Delete it!
    $Item->delete();

    return $this->success("<strong>Deleted!</strong>");
  }
}
