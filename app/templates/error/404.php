<?php

# + Snow.
include SNOW;

# + Header partial.
include TEMPLATE . "/global/_basic-header.php"; ?>

<content begin fl fldircol alic jucsb gap>
  <div flone fl fldircol alic jucc style="max-width:600px;">

    <div style="margin-bottom:-2.4em;height:300px;width:300px;">
      <?php

      # + Ghost animation.
      include TEMPLATE . "/global/_lottie-pixelghost.html"; ?>
    </div>

    <div mb=mid tac>
      <p text bold wider>Huh?</p>
      <p text>You have entered an unknown path. Better return!</p>
    </div>

    <mbutton material size=mid filled=lighter has-icon=left color=dynamic
      onclick="history.go(-1);">
      <mi>west</mi>
      <p text bold>Go back</p>
    </mbutton>
  </div>

  <?php

  # + Basic footer with links.
  include TEMPLATE . "/global/_basic-footer.php"; ?>
</content>