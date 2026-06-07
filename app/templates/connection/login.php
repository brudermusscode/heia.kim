<?php

/**
 * @var string
 */
$provider = filter_var($GLOBALS["route_param_provider"], FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$state = filter_input(INPUT_GET, "state", FILTER_SANITIZE_SPECIAL_CHARS);

?>

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

  <request
    action="connection:reconnect"
    data-provider="<?= $provider ?>"
    data-code="<?= $code ?>"
    data-state="<?= $state ?>"
    method="POST" redirect-from-data="full" redirect-on-error="/login" responder=error
    audio-success="bell-highpitch-audio" audio-error="bell-negative-audio"></request>

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