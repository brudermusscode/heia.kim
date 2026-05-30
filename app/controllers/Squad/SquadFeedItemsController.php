<?php

namespace Bruder\Heiakim\Controller\Squad;

use Bruder\Controller;
use Bruder\Heiakim\Model\Squad\SquadFeedItem;

class SquadFeedItemsController extends Controller
{

  /**
   * DELETE
   *
   * @return object
   */
  public function delete()
  {

    /**
     * Params valid?
     */
    $this->validate_params(["id"]);

    /**
     * @var ?SquadFeedItem
     */
    $Item = SquadFeedItem::findOrReturn($this->params->id, "<strong>This feed item doesn't exist!</strong> It might have been deleted.");

    /**
     * SquadUser can interact with this item?
     */
    $this->can_interact(
      resource: $this->CurrentUser?->squad_user,
      item: $Item,
    );

    /**
     * Delete it!
     */
    $Item->delete();

    return $this->success("<strong>Deleted!</strong>");
  }
}
