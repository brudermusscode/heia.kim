<?php

namespace Heiakim\Controller\Order;

use Heiakim\Controller\Controller;
use Heiakim\Model\Order;
use Heiakim\Model\Order\OrderPaypal;
use Heiakim\Model\User;

class OrderPaypalController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["months"],
      optional: ["user_id"],
    );

    $this->authorize();

    /**
     * @var ?User
     */
    $User = User::find($escaped_params->user_id ?? 0);

    $error_message = $User ? "<strong>This user is currently socially excluded.</strong> Wait for them to be reintegrated." : "!SOCIALLY_EXCLUDED";

    # Any of the Users is socially excluded?
    if (CurrentUser->is_socially_excluded() || $User && $User->is_socially_excluded())
      return $this->error($error_message);

    # Append params.
    $this->params->CurrentUser = CurrentUser;

    return (new OrderPaypal)->new($this->params);
  }

  /**
   * @return object
   */
  public function capture(array $params)
  {


    $this->validate_params(
      strict: ["id", "capture_url"],
      optional: ["user_id"],
    );

    $this->authorize();

    $this->return->status = 8;

    /**
     * Params valid?
     */
    if (!$escaped_params)
      return $this->error();

    /**
     * @var ?Order
     */
    $Order = CurrentUser
      ->orders()
      ->whereHas("paypal")
      ->find($escaped_params->id);

    if (!$Order)
      return $this->error();

    return $Order->paypal->capture($escaped_params);
  }
}
