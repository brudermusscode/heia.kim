<?php

namespace Bruder\Heiakim\Model\Payment;

use Bruder\Application\Application;
use Bruder\Heiakim\Trait\ProcessesRequests;

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
