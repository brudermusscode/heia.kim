<?php

namespace Heiakim\Model;

use Exception;
use Heiakim\Application\Logger;
use Heiakim\Enum\Privilege;
use Heiakim\Justin;
use Heiakim\Model\User;
use Heiakim\Trait\HasDefaultUser;
use Heiakim\Utils\Utils;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use Psr\Log\LogLevel;

class Order extends Justin
{
  use HasDefaultUser;

  /**
   * @var array
   */
  protected $fillable = [
    "user_id",

    # When someone orders for another user, the action taking user is user_2.
    "user_2_id",
    "order_id",
    "provider_user_id",
    "order_status",
    "order_amount",
    "order_pieces",
    "order_currency",
    "order_links",
  ];

  /**
   * @param object $params
   * @return string
   */
  public function new(object $params)
  {

    /**
     * @var User
     */
    $BenefittedUser = $params->User;

    $to_pay = (int) $params->months * PREMIUM_PRICE;
    $months = (int) $params->months;

    /**
     * @var Order
     */
    $Order = $BenefittedUser->orders()->make();
    $Order->user_2_id = $params->is_gift ? CurrentUser->id : null;
    $Order->order_amount = $to_pay;
    $Order->order_pieces = $months;
    $Order->order_currency = CURRENCY;

    # Initialize a PayPal SDK Client.
    $c = oauth_credentials("paypal");
    $Client = PaypalServerSdkClientBuilder::init()
      ->clientCredentialsAuthCredentials(
        ClientCredentialsAuthCredentialsBuilder::init(
          $c[current_env()]["client_id"],
          $c[current_env()]["client_secret"],
        )
      )
      ->environment(Environment::SANDBOX)
      ->build();

    # Create a new Order through PayPal API.
    $response = $Client->getOrdersController()
      ->createOrder([
        'body' => [
          'intent' => 'CAPTURE',
          'purchase_units' => [[
            'amount' => [
              'currency_code' => CURRENCY,
              'value' => $to_pay,
            ],
          ]],
          'payment_source' => [
            'paypal' => [
              'experience_context' => [
                'return_url' => 'http://localhost:81/order/success',
                'cancel_url' => 'http://localhost:81/order/cancel',
              ],
            ],
          ],
        ],
      ]);

    # Creating an Order failed?
    if (!$response->isSuccess())
      return die(error("Order creation failed.", data: $response->getResult()));

    $ProviderOrder = $response->getResult();

    $Order->order_id = $ProviderOrder->getId();
    $Order->order_status = $ProviderOrder->getStatus();
    $Order->order_links = $ProviderOrder->getLinks()[1]->getHref();
    $Order->save();

    return $this->success(
      "<strong>Bestätige Deine Bestellung über das PayPal-Fenster.</strong>",
      data: [
        "Order" => $Order,
        "link" => $ProviderOrder->getLinks()[1]->getHref()
      ]
    );
  }

  /**
   * @param object $params
   * @return static
   *
   * NOTE: Will die on error.
   */
  public function edit(object $params)
  {

    # Start a database transaction!
    $this->db_transaction();

    try {

      # Initialize a PayPal SDK Client.
      $c = oauth_credentials("paypal");
      $Client = PaypalServerSdkClientBuilder::init()
        ->clientCredentialsAuthCredentials(
          ClientCredentialsAuthCredentialsBuilder::init(
            $c[current_env()]["client_id"],
            $c[current_env()]["client_secret"],
          )
        )
        ->environment(Environment::SANDBOX)
        ->build();

      # Create a new Order through PayPal API.
      $response = $Client->getOrdersController()
        ->captureOrder(["id" => $this->order_id]);

      # Getting ProviderOrder failed?
      if (!$response->isSuccess())
        return die(error("Could not get order."));

      $ProviderOrder = $response->getResult();

      # Order is not yet completed?
      if ($ProviderOrder->getStatus() !== "COMPLETED")
        return die(error("capturing"));

      # Update the order to be done & set some other values.
      $this->order_status = $ProviderOrder->getStatus();
      $this->provider_user_id = $params->provider_user_id ?? null;
      $this->order_links = null;
      $this->save();

      $this->db_commit();

      return $this;
    } catch (\Exception $e) {
      Logger::to_file($e);
      $this->db_rollback();

      return die(error("Something went wrung! " . TRY_OR_STAFF));
    }
  }

  /**
   * @return HasOne<User>
   */
  public function payer()
  {
    return $this->hasOne(User::class, "id", "user_2_id");
  }
}
