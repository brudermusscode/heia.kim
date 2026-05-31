<?php

namespace Heiakim\Model\Payment;

use Heiakim\Application\Application;
use Heiakim\Trait\ProcessesRequests;

class Payment
{
  use ProcessesRequests;

  /**
   * @param string $vendor
   * @return ?array
   */
  public function get_credentials(string $vendor)
  {
    $arr = require _root() . "/config/security/oauth.php";

    return $arr[$vendor] ?? [];
  }
}
