<?php

namespace Bruder\Heiakim\Controller\Mailings;

use Bruder\Http\Request;
use Bruder\Controller;
use Bruder\Heiakim\Model\Mailing;

class MailingsController extends Controller
{
  /**
   * UPDATE
   *
   * @param array $params The params.
   * @return object Default return object.
   */
  public function edit(array $params)
  {
    $escaped_params = $this->serialize_request_params(["token"], $params, []);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * Mailing exists?
     */
    $Mailing = Mailing::where("token", $escaped_params->token)->first();
    if (!$Mailing)
      return $this->error("<strong>The mailing does not exist.</strong>");

    return $Mailing->touch();
  }

  /**
   * Serialize GET or POST parameters
   *
   * @return object
   */
  private function sanitize_request(array $params)
  {
    return $this->serialize_request_params([], $params, []);
  }
}
