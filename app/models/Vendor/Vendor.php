<?php

namespace Heiakim\Model\Vendor;

class Vendor
{

  /**
   * @var ?array
   */
  protected $credentials = null;

  /**
   * @param string $vendor
   * @return ?array
   */
  public function oauth_credentials(string $vendor)
  {
    $arr = require _root() . "/config/security/oauth.php";

    return $arr[$vendor] ?? [];
  }
}
