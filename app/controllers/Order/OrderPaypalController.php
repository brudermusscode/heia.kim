<?php

namespace Bruder\Heiakim\Controller\Order;

use Bruder\Application\Application;
use Bruder\Application\Setting;
use Bruder\Http\Request;
use Bruder\Controller;
use Bruder\Heiakim\Model\Order;
use Bruder\Heiakim\Model\Order\OrderPaypal;
use Bruder\Heiakim\Model\User;
use Bruder\Utils\Utils;

class OrderPaypalController extends Controller
{

  /**
   * POST
   *
   * @param array $params
   * @return object
   */
  public function create(array $params)
  {
    $escaped_params = $this->serialize_request_params(["months"], $params, ["user_id"]);

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error("!FIELDS_MISSING");

    /**
     * Logged in & verified?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * @var ?User
     */
    $User = User::find($escaped_params->user_id ?? 0);

    /**
     * @var string
     */
    $error_message = $User ? "<strong>This user is currently socially excluded.</strong> Wait for them to be reintegrated." : "!SOCIALLY_EXCLUDED";

    /**
     * Both users are verified?
     */
    if ($this->CurrentUser->is_socially_excluded() || $User && $User->is_socially_excluded())
      return $this->error($error_message);

    /**
     * Append necessary params.
     */
    $this->params->CurrentUser = $this->CurrentUser;

    return (new OrderPaypal)->new($escaped_params);
  }

  /**
   * @param array $params
   * @return object
   */
  public function capture(array $params)
  {
    $escaped_params = $this->serialize_request_params(["id", "capture_url"], $params, []);
    $this->return->status = 8;

    /**
     * Logged in & verified?
     */
    if (!$this->CurrentUser)
      return $this->error("!NOT_LOGGED");

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * @var ?Order
     */
    $Order = $this->CurrentUser
      ->orders()
      ->whereHas("paypal")
      ->find($escaped_params->id);

    if (!$Order)
      return $this->error();

    return $Order->paypal->capture($escaped_params);
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
