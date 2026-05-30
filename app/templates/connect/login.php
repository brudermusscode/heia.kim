<?php

/**
 * GET parameter.
 */

use Bruder\Heiakim\Controller\Connect\ConnectDiscordController;
use Bruder\Heiakim\Controller\Connect\ConnectGoogleController;
use Bruder\Heiakim\Controller\Connect\ConnectOsuController;

$vendor  = filter_var(GET["vendor"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
$code    = filter_var(GET["code"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
$state   = filter_var(GET["state"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Invalid vendor or missing GET param?
 */
if (
  !in_array(
    $vendor,
    [
      "osu",
      "discord",
      "google",
    ]
  ) || !$code || !$state
)
  include __DIR__ . "/pages/_invalid.php";
else {

  /**
   * @var object
   */
  $Vendor = match ($vendor) {

    /**
     * ? osu!
     */
    "osu" => (new ConnectOsuController)->login([
      "code" => $code,
      "state" => $state,
    ]),

    /**
     * ? Discord
     */
    "discord" => (new ConnectDiscordController)->login([
      "code" => $code,
      "state" => $state,
    ]),

    /**
     * ? Google
     */
    "google" => (new ConnectGoogleController)->login([
      "code" => $code,
      "state" => $state,
    ]),
  };

  /**
   * @var bool
   */
  $failed = !$Vendor->status;

  /**
   * Heading
   */
  include TEMPLATE . "/login/_header.php";

?>

  <login>

    <?php include SNOW; ?>

    <div disguised-content content-width=smolest fl fldircol gap>

      <!--- FLEX: MAIN CONTENT --->
      <div style="flex:1;flex-direction:row-reverse;" gap justcontcent>
        <div class="login_right" fl fldircol gap=mid flexone>
          <sign-container fl fldircol gap=mid>

            <?php if (!$failed) { ?>

              <redirect to="/home"></redirect>

            <?php

            } else {

            ?>

              <box-model rounded="wide" filled="lighter" p62 fl fldircol alic gap>
                <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled>
                  <mi wide>error</mi>
                </div>
                <div tac fl fldircol gap=smoler>
                  <p text mid>Login failed</p>
                  <p text std><?= $Vendor->message; ?></p>
                </div>
                <a href="/home">
                  <mbutton material ripple-effect has-icon="left" size="mid" filled>
                    <mi>arrow_back</mi>
                    <p text std>Go home</p>
                  </mbutton>
                </a>
              </box-model>

            <?php } ?>

          </sign-container>
        </div>
      </div>
    </div>

  </login>

<?php

}
