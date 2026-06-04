<?php

/**
 * This class offers funtctionality to interact with vendor API's in a general
 * fashion without user specific grant.
 */

namespace Heiakim\Model;

use Heiakim\Justin;
use Heiakim\Exception\ApiException;
use Heiakim\Trait\IsApiProvider;

abstract class ApiProvider extends Justin
{
  use IsApiProvider;

  protected const PROVIDER = "";

  /**
   * @return static
   * @throws ApiException
   */
  public static function get()
  {
    return static::where("provider", static::PROVIDER)->first()
      ?? throw new ApiException("ApiProvider »" . static::PROVIDER . "« not available.");
  }
}
