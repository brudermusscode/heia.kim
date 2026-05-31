<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Begin the outpuff buffer.
 */
ob_start();

?>

<div content-width=smolest prompt-height>

  <box-model prompt elevated rounded="wide" filled=lighter>
    <div prompt-content>

      <div prompt-header>
        <mi>error</mi>
        <p title>Sh*#%t!</p>
      </div>

      <div prompt-inner-content fl fldircol gap>
        <?php if ($type === "profileeditor") { ?>
          <p text>The <strong>Profile Editor</strong> is currently only <strong>available on Desktop</strong> devices. It might come to smaller devices in the future.</p>
        <?php } ?>
      </div>
    </div>

    <div prompt-actions>
      <div></div>
      <mbutton close-overlay background=slight material size=mid>
        <p text bold>Too bad!</p>
      </mbutton>
    </div>
  </box-model>

</div>

<?php

die($Request->success(data: ob_get_clean()));
