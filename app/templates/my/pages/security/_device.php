<?php

use Heiakim\Time\Time;
use Heiakim\Model\Session;
use Heiakim\Application\Cookie;

/**
 * @var ?Session
 */
$Session = CurrentUser->sessions()
  ->where("id", $id)
  ->first();

if (!$Session) :
  echo "<redirect to='$base_url'";
else :

  /**
   * @var bool
   */
  $is_current_session = $Session->token === Cookie::get(Session::$persistent_cookies[1]);

?>

  <div content-width=smol>
    <div mt=wide mb fl gap=mid align-items="center" mb=std>
      <?php include TEMPLATE . "/my/_back_button.php"; ?>

      <label size="mid" has-secondary>
        <div class="label__main">
          <p bold><?= __("Device") ?></p>
        </div>
      </label>
    </div>

    <div fl fldircol gap>
      <box-model filled>

        <box-model filled=darker>
          <bm-inr size=mid fl gap alic>
            <div circled fl alic jucc style=height:4.2em;width:4.2em; filled=lighter>
              <mi wide class="<?= $Session->display()->icon_class; ?>"></mi>
            </div>
            <div fl fldircol gap=smol>

              <div fl gap=smol alic>
                <p text midler bold><?= $Session->display()->os_full; ?></p>
              </div>

              <?php if (!$Session->deleted_at) { ?>
                <div fl gap=smol+ alic>
                  <mi size=spec color=green>toggle_on</mi>
                  <p text><?= __("Active since") ?> <strong><?= Time::ago($Session->created_at); ?></strong>
                  </p>
                </div>

                <?php if ($Session->updated_at) { ?>
                  <div fl gap=smol+ alic>
                    <mi size=spec>update</mi>
                    <p text><?= __("Last accessed") ?>
                      <strong><?= Time::ago($Session->updated_at, true); ?></strong></strong>
                    </p>
                  </div>
                <?php } ?>

                <?php if ($is_current_session) { ?>
                  <div fl gap=smol+ alic>
                    <mi size=spec color=blue>check_circle</mi>
                    <p text><?= __("Current session") ?></p>
                  </div>
                <?php } ?>

              <?php } else { ?>
                <div fl gap=smol+ alic>
                  <mi size=spec color=red>toggle_off</mi>
                  <p text><?= __("Inactive") ?></p>
                </div>
              <?php } ?>
            </div>
          </bm-inr>
        </box-model>

        <bm-inr size=wide fl fldircol gap>
          <?php if (!$Session->deleted_at) { ?>
            <div fl jucend>
              <form <?= !$is_current_session ? 'request="session:delete"' : 'data-form="session:delete"' ?> redirect="<?= $base_url ?>" responder>
                <input type=hidden name=token value="<?= $Session->token; ?>" />
                <mbutton material size=mid submit-closest has-icon=left background=unfollow color=dark-red>
                  <mi>logout</mi>
                  <p text bold><?= __("Logout") ?></p>
                </mbutton>
              </form>
            </div>
          <?php } ?>
        </bm-inr>
      </box-model>
    </div>
  </div>

<?php endif; ?>