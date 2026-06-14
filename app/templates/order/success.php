<?php

/**
 * @var string
 */

use Heiakim\Model\Order;

$order_id = filter_var($_GET["token"] ?? "");

/**
 * @var string
 */
$provider_user_id = filter_var($_GET["PayerID"] ?? "");

$Order = Order::where("order_id", $order_id)->first();

# Order does not exist?
if (!$Order) :
  include UNAVAILABLE;
else :

  # Build request attributes.
  $request = [
    "action" => "order:update",
    "method" => "POST",
    "data-order-id" => $order_id,
    "data-provider-user-id" => $provider_user_id,
    "on-success" => <<<JAVASCRIPT
      window.opener.postMessage({
        type: 'success',
        order_id: '$order_id'
      }, window.location.origin);
      window.close();
    JAVASCRIPT,
  ];

  # Start the request.
  include REQUEST; ?>

  <content min-full fl fldircol alic jucc>
    <div material-bar-loader class="linear-progress-material">
      <div class="bar bar1"></div>
      <div class="bar bar2"></div>
    </div>

    <div style="height:180px;width:180px;">
      <?php

      # + Loading animation.
      include TEMPLATE . "/global/_lottie-pixelghost.html"; ?>
    </div>
  </content>

<?php endif; ?>