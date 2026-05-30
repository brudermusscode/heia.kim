<?php

use Bruder\Heiakim\Model\Squad\SquadRequest;

/**
 * @var ?SquadRequest
 */
$SquadJoinRequests = $Squad->active_requests()
  ->where("type", "join")
  ->get();

?>

<?php

foreach ($SquadJoinRequests ?? [] as $Request)
  include dirname(dirname(__DIR__)) . "/_request.php";

?>

<box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap flexone none>
  <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
    <i class="mi" size="wide">south_west</i>
  </div>
  <div tac>
    <p text bold wide>Nothing</p>
    <p text std><?= __("Nobody wants to join your squad") ?></p>
  </div>
</box-model>