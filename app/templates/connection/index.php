<?php

/**
 * @var string
 */
$provider = filter_var($GLOBALS["route_param_provider"]);

/**
 * @var string
 */
$code = filter_input(INPUT_GET, "code");

/**
 * @var string
 */
$state = filter_input(INPUT_GET, "state");

# Decode the state as it will contain our action to run in the following.
$action = base64_decode($state);

# Build the attributes including values for the <request> element based ont the action
# set in the state.
$request = match ($action) {
  "connect" => [
    "action" => "connection:create",
    "redirect-on-error" => "/register",
    "redirect-from-data" => "",
  ],
  "reconnect" => [
    "action" => "connection:reconnect",
    "redirect-on-error" => "/login",
    "redirect-from-data" => "full",
    "audio-success" => "bell-highpitch-audio",
    "audio-error" => "bell-negative-audio",
  ],
  default => null,
};

# Include unavailable template, if there is no valid action set.
if (!$request) :
  include UNAVAILABLE;
else : ?>

  <!--- The request validating code, state and action for the given provider. --->
  <request
    <?php foreach ($request as $attr => $value) : ?>
    <?= "$attr=\"$value\"" ?>
    <?php endforeach; ?>
    data-provider="<?= $provider ?>"
    data-code="<?= $code ?>"
    data-state="<?= $state ?>"
    method="POST" responder=error>
  </request>

  <content min-full fl fldircol alic jucc>
    <div>
      <div style="height:180px;width:180px;">
        <?php

        # + Loading animation.
        include TEMPLATE . "/global/_lottie-pixelghost.html"; ?>
      </div>

      <div material-bar-loader class="linear-progress-material" style="position:absolute;top:0;left:0;width:100vw;">
        <div class="bar bar1"></div>
        <div class="bar bar2"></div>
      </div>
    </div>

    <div dno>
      <div fl fldircol alic jucc>
        <p text bold mid>Success brother!</p>
        <p text>… Redirecting you back in <span text bold color=company id="counter"></span> …</p>
      </div>

      <!--<redirect to="/my/security/<?= $type ?>" delay=5000></redirect>-->

      <!--<script>
      let sec = 5
      let el = document.getElementById("counter")

      el.innerHTML = sec;

      let timer = setInterval(() => {
        sec--
        el.textContent = sec
        if (sec <= 0) clearInterval(timer)
      }, 1000)
    </script>-->
    </div>

  </content>

<?php endif; ?>