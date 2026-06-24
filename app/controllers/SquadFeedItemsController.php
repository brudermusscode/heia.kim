<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Squad\SquadFeedItem;

class SquadFeedItemsController extends Controller
{

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"]
    );

    /**
     * @var ?SquadFeedItem
     */
    $Item = SquadFeedItem::findOrReturn($this->params->id);

    $this->can_interact(
      resource: CurrentUser?->squad_user,
      item: $Item,
    );

    $Item->delete();

    return success(data: ["FeedItem" => $Item]);
  }
}
