<?php

use Heiakim\Time\Time;
use Heiakim\Model\Session;
use Heiakim\Application\Cookie;

/**
 * @var string $category
 * @var string $sub
 * @var string $var
 */

/**
 * @var ?Session
 */
$Session = CurrentUser->sessions()
  ->where("id", $var)
  ->first();

if (!$Session) :
  echo "Nö";
else :

  $is_current_session = $Session->token ===
    Cookie::get(Session::$persistent_cookies[1]);

?>

  <div fl fldircol gap>
    <div fl alic gap>
      <?php include TEMPLATE . "/my/_back_button.php"; ?>

      <div fl alic gap=smolest>
        <p text mid bold><?= $Session->masked_ip() ?>.</p>
        <p pinline8 pblock12 background=slight rounded></p>
        <p pinline8 pblock12 background=slight rounded></p>
      </div>
    </div>

    <div fl fldircol gap posrel pl64>
      <div style="width:4px;height:100%;top:0;left:28px;" posabs rounded background=slight></div>

      <div>
        <?php if (!$Session->deleted_at) { ?>
          <div fl gap=smol+ alic>
            <mi color=green>toggle_on</mi>
            <p text><?= __("Active") ?> &middot;
              <strong color=company><?= Time::ago($Session->created_at); ?></strong>
            </p>
          </div>

          <?php if ($Session->updated_at) { ?>
            <div fl gap=smol+ alic>
              <mi>update</mi>
              <p text><?= __("Last accessed") ?> &middot;
                <strong color=company><?= Time::ago($Session->updated_at, true); ?></strong>
              </p>
            </div>
          <?php } ?>

        <?php } else { ?>
          <div fl gap=smol+ alic>
            <mi color=red>toggle_off</mi>
            <p text><?= __("Inactive") ?></p>
          </div>
        <?php } ?>
      </div>
    </div>

    <div fl alistart jucsb>
      <?php if ($is_current_session) : ?>
        <div fl gap=smol alic p6 pr20 filled rounded=mid>
          <mi mid color=blue>check_circle</mi>
          <p text midler><?= __("Current") ?></p>
        </div>
      <?php else : ?>
        <div fl gap=smol alic p6 pr20 filled rounded=mid>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
      <?php endif; ?>

      <?php if (!$Session->deleted_at) : ?>
        <div fl jucend>
          <mbutton mid has-icon=left background=unfollow color=dark-red
            request="session:delete" shadow-submit
            data-token="<?= $Session->token; ?>"
            <?= $is_current_session ? "full-redirect='/'" : "um-open='security'" ?> responder=error>
            <mi>logout</mi>
            <p text bold><?= __("Logout") ?></p>
          </mbutton>
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php endif; ?>