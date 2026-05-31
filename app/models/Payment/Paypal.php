<?php

namespace Heiakim\Model\Payment;

use Heiakim\Application\Application;
use Heiakim\Http\Request;
use Heiakim\Model\Order;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalHttp\HttpException;

class PayPal extends Payment implements PaymentInterface
{
  /**
   * @var string
   */
  protected $table = null;

  /**
   * Creates an environment, either Sandbox or Live with given
   * credentials and returns an http client.
   *
   * @return PayPalHttpClient
   */
  public function get_connection()
  {
    /**
     * App environment
     */
    $environment = current_env();

    /**
     * Get the credentials.
     */
    $credentials = $this->get_credentials("paypal")[$environment == "dev" ? "sandbox" : "live"];

    /**
     * Save client id and secret in variables.
     */
    $client_id = $credentials["client_id"];
    $client_secret = $credentials["client_secret"];

    /**
     * Set up the environment that will be used in the request
     * later based on the app environment.
     */
    $environment = $environment == "dev"
      ? new SandboxEnvironment($client_id, $client_secret)
      : new ProductionEnvironment($client_id, $client_secret);

    return new PayPalHttpClient($environment);
  }

  /**
   * @param object $params The params.
   * @return object Default return object.
   */
  public function new(object $params)
  {
    /**
     * @var Order
     */
    $Order = $params->order;

    /**
     * @var User
     */
    $CurrentUser = $params->CurrentUser;

    $environment = current_env();
    $credentials = $this->get_credentials("paypal")[$environment == "dev" ? "sandbox" : "live"];
    $client = $this->get_connection();

    /**
     * Prepare the request to API.
     */
    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');
    $request->body = [
      "intent" => "CAPTURE",
      "purchase_units" => [
        [
          "reference_id" => $Order->order_id,
          "amount" => [
            "value" => $Order->order_amount,
            "currency_code" => $Order->currency
          ]
        ]
      ],
      "application_context" => $credentials["return_uris"],
    ];

    try {
      $results = $client->execute($request);
    } catch (HttpException $e) {
      return $this->error("!UNKNOWN_ERROR");
    }

    /**
     * Generating links failed? Bycheck all the variables we need
     * for order creation.
     */
    if (
      $results->statusCode !== 201
      || !isset(
        $results->result->id,
        $results->result->status,
        $results->result->intent
      )
    ) {
      return $this->error("!UNKNOWN_ERROR");
    }

    /**
     * Pass the result data to the return object.
     */
    $this->return->data = $results->result;

    return Request::modoru($this->return);
  }

  /**
   * @param object $params The params.
   * @return object Default return object.
   *
   * Status 8: Error
   * Status 10: Success
   * All others: Uncaptured
   */
  public function capture(object $params)
  {
    $client = $this->get_connection();
    $request = new OrdersCaptureRequest($params->paypal_order_id);
    $request->prefer('return=representation');

    try {
      $response = $client->execute($request);
    } catch (HttpException $ex) {
      /**
       * If the statusCode is not 422, there is a different error.
       * Return a local error to the user.
       */

      if ($ex->statusCode !== 422) {
        $this->return->status = 8;
        return $this->error("!UNKNOWN_ERROR");
      } else {
        $this->return->status = false;
        return $this->error("<strong>Order not yet captured.</strong>");
      }
    }

    if (!isset($response->result->payer->payer_id)) {
      $this->return->status = 8;
      return $this->error("!UNKNOWN_ERROR");
    }

    /**
     * Append the captire results to the return object.s
     */
    $this->return->data = $response->result;

    return Request::modoru($this->return);
  }

  /**
   * @param object $params The params.
   * @return object Default return object.
   */
  public function success(object $params) {}
}
