<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Enum\Privilege;
use Heiakim\Model\Order;
use Heiakim\Model\User;

class OrdersController extends Controller
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

    # Social exclusion is checked after the User has been initialized as either the
    # CurrentUser can create an Order for themselves or for another User.
    $this->authorize();

    /**
     * @var ?User
     */
    $User = User::find($this->params->user_id ?? 0) ?? CurrentUser;

    # User is not scoially viable?
    if ($User->is_socially_excluded())
      return error($User->is(CurrentUser) ? "!SOCIALLY_EXCLUDED" : "!SOCIALLY_EXCLUDED_THIRD");

    # Less than 1 month set?
    if ($this->params->months < 1)
      return error("<strong>Buy atleast 1 month!</strong> Otherwise it makes no sense.");

    $this->params->User = $User;
    $this->params->is_gift = !$User->is(CurrentUser);

    return (new Order)->new($this->params);
  }

  /**
   * @return string
   */
  public function update()
  {


    $this->validate_params(
      strict: ["order_id"],
      optional: ["provider_user_id"]
    );

    $this->authorize();

    /**
     * Either get a normal Order or a gifted one.
     * @var ?Order
     */
    $Order = CurrentUser->orders()
      ->with("user")
      ->where("order_id", $this->params->order_id)
      ->first()
      ?? CurrentUser->gifted_orders()
      ->with("user")
      ->where("order_id", $this->params->order_id)
      ->first();

    # Order doesn't exist?
    if (!$Order || $Order->order_status === "COMPLETED")
      return error();

    # Update the Order, this will die on error.
    $Order->edit($this->params)->fresh();

    # Give the benefitted User Premium relation.
    if (!$Order->user->premium)
      $Order->user->premium()->create();

    # What time is the highest, donor_end or the current time.
    $time = max($Order->user->donor_end, time());

    # Update the User!
    $Order->user->update([
      "donor_end" => strtotime("+{$Order->order_pieces} month", $time),
    ]);

    # Give supporter privileges.
    $Order->user->add_privileges(Privilege::SUPPORTER);

    # Give extras:
    # +1 name change
    # +1 restart
    $Order->user->settings->increment("name_changes_left");
    $Order->user->settings->increment("account_wipes_left");

    # TODO: Give discord premium role.

    return success();
  }


  /**
   * @return string
   */
  public function capture()
  {

    $this->validate_params(
      strict: ["order_id"],
    );

    $this->authorize();

    /**
     * Either get a normal Order or a gifted one.
     * @var ?Order
     */
    $Order = CurrentUser->orders()
      ->where("order_id", $this->params->order_id)
      ->first()
      ?? CurrentUser->gifted_orders()
      ->where("order_id", $this->params->order_id)
      ->first();

    # Order doesn't exist?
    if (!$Order)
      return error("no_order");

    # Order not yet captured?
    if ($Order->order_status !== "COMPLETED")
      return error("not_captured");

    return success("<strong>Enjoy your extra features, my friend!</strong> 🙂");
  }

  /**
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["order_id"],
    );

    $this->authorize();

    /**
     * Either get a normal Order or a gifted one.
     * @var ?Order
     */
    $Order = CurrentUser->orders()
      ->where("order_id", $this->params->order_id)
      ->first()
      ?? CurrentUser->gifted_orders()
      ->where("order_id", $this->params->order_id)
      ->first();

    # Orders should only be deletable when they are not yet completed.
    if (!$Order || $Order->order_status === "COMPLETED")
      return error("Can't delete order!");

    $Order->delete();

    return success("Order deleted!");
  }
}
