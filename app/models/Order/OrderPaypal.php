<?php

namespace Heiakim\Model\Order;

use Heiakim\Application\Exception;
use Heiakim\Application\Logger;
use Heiakim\Application\Setting;
use Heiakim\Http\Request;
use Heiakim\Justin;
use Heiakim\Enum\Privilege;
use Heiakim\Model\Order;
use Heiakim\Model\Payment\PayPal;
use Heiakim\Time\Time;
use Heiakim\Model\User;
use Heiakim\Model\Connect\ConnectDiscord;
use Heiakim\Utils\Utils;

class OrderPaypal extends Justin
{
  /**
   * @var string
   */
  protected $table = "order_paypal";

  /**
   * @var array
   */
  protected $fillable = [
    "order_id",
    "paypal_order_id",
    "paypal_order_status",
    "paypal_url_order",
    "paypal_url_approve",
    "paypal_url_update",
    "paypal_url_capture",
    "deleted_at",
    "updated_at",
  ];

  /**
   * CREATE
   *
   * @param object $params
   * @return object
   */
  public function new(object $params)
  {
    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    /**
     * @var ?User
     */
    $User = !empty($params->user_id) ? User::find($params->user_id) : null;

    /**
     * Evaluate the user to benefit from the order.
     *
     * @var User
     */
    $BenefittedUser = $User ?? $CurrentUser;

    /**
     * @var Setting
     */
    $Settings = Setting::first();

    /**
     * @var int
     */
    $order_id = Utils::random_numeric_token(10);

    /**
     * Ensure that the reference_id is unique.
     */
    while (
      Order::where('order_id', $order_id)
      ->exists()
    )
      $order_id = Utils::random_numeric_token(10);

    /**
     * @var int
     */
    $months = (int) $params->months;

    /**
     * Months are higher than 0?
     */
    if ($months < 1)
      return $this->error("<strong>Buy atleast 1 month!</strong> Otherwise it makes no sense.");

    /**
     * @var Order
     */
    $Order = $CurrentUser
      ->orders()
      ->create([
        "reference_id" => $BenefittedUser->id !== $CurrentUser->id ? $BenefittedUser->id : $CurrentUser->id,
        "order_id" => $order_id,
        "order_amount" => $months * $Settings->premium_feature_price,
        "order_status" => "OPEN",
        "currency" => $Settings->currency,
        "order_pieces" => $months,
      ]);
    $Order = $Order->fresh();

    /**
     * Append the order to the params object.
     */
    $params->order = $Order;

    /**
     * Create new request to the PayPal API and retrieve result
     * data to create the new OrderPayPal.
     */
    $results = (new PayPal)->new($params);
    if (!$results->status)
      return $results;

    $Order->paypal()
      ->create([
        "paypal_order_id" => $results->data->id,
        "paypal_order_status" => $results->data->status,
        "paypal_url_order" => $results->data->links[0]->href,
        "paypal_url_approve" => $results->data->links[1]->href,
        "paypal_url_update" => $results->data->links[2]->href,
        "paypal_url_capture" => $results->data->links[3]->href,
        "updated_at" => null,
      ]);

    $this->return->links = $results->data->links;
    $this->return->id = $Order->id;

    return $this->success("<strong>Bestätige Deine Bestellung über das PayPal-Fenster.</strong>");
  }

  /**
   * UPDATE
   *
   * @param object $params
   * @return object
   */
  public function capture(object $params)
  {
    /**
     * @var Order
     */
    $Order = $this->order;

    /**
     * @var User
     */
    $CurrentUser = $this->order->user;

    /**
     * @var User
     */
    $BenefittedUser = $Order->reference ?? $CurrentUser;

    /**
     * Append the PayPal Order ID to the params object.
     */
    $params->paypal_order_id = $this->paypal_order_id;

    /**
     * Order already completed?
     */
    if ($this->paypal_order_status == "COMPLETED") {
      $this->return->status = 4;
      return $this->return;
    }

    /**
     * Create PayPal API request for capturing the order placement.
     */
    $results = (new PayPal)->capture($params);

    /**
     * Begin new database transaction.
     */
    $this->db_transaction();

    try {

      /**
       * If order has been captured, update this instance with the
       * missing paypal information.
       */
      if ($results->status)
        $this->update([
          "paypal_order_status" => $results->data->status,
          "paypal_payer_id" => $results->data->payer->payer_id,
        ]);
      else
        return $results;

      /**
       * Set the order to payed.
       */
      $Order
        ->update([
          "order_status" => "DONE",
        ]);

      /**
       * CVreate user's premium settings.
       */
      if (!$BenefittedUser->premium)
        $BenefittedUser->premium()
          ->create();

      /**
       * @var int
       */
      $months = $Order->order_pieces;

      /**
       * Update users donor_end.
       */
      $time_left = $BenefittedUser->donor_end > time();
      $datetime_months = "+$months month" . ($months > 1 ? "s" : "");
      $donor_end =  strtotime($datetime_months, $time_left ? $BenefittedUser->donor_end : time());

      /**
       * Update the user's premium timestamp.
       */
      $BenefittedUser->update([
        "donor_end" => $donor_end,
      ]);

      /**
       * Give supporter privileges.
       */
      $BenefittedUser->add_privileges(Privilege::SUPPORTER);

      /**
       * Give extras:
       * +1 name change
       * +1 restart
       */
      $BenefittedUser->settings()
        ->update([
          "name_changes_left" => (int) $BenefittedUser->settings->name_changes_left + 1,
          "account_wipes_left" => (int) $BenefittedUser->settings->account_wipes_left + 1,
        ]);

      /**
       * Give Premium+ role on Discord.
       */
      $BenefittedUser->discord
        ?->give_role(role_id: 1147920868907946045);

      /**
       * Commit all database transaction changes.
       */
      $this->db_commit();
    } catch (\Exception $e) {
      Logger::to_file($e);
      /**
       * Rollback all database transaction changes.
       */
      $this->db_rollback();
    }

    $this->return->message = "<strong>Enjoy your extra features, my friend!</strong>";
    $this->return->status = 10;

    return $this->return;
  }

  /**
   * DELETE
   *
   * @param object $params
   * @return object
   */
  public function remove(object $params) {}

  /**
   * @return Order
   */
  public function order()
  {
    return $this->belongsTo(Order::class);
  }
}
