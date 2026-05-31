<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;
use Heiakim\Model\User;
use Heiakim\Time\Time;

/**
 * @var User $CurrentUser
 * @var Request $Request
 */

/**
 * User logged?
 * ! Error
 */
if (!LOGGED)
  exit($Request->error("!NOT_LOGGED"));

/**
 * Begin output buffer.
 */
ob_start();

/**
 * @var ?string
 */
$premium_time_left = Time::left($CurrentUser->donor_end)

?>

<user-menu filled=darker rounded=wider elevated animation=open>
  <div p24 fl fldircol gap=smol+>

    <div fl fldircol gap=smol>
      <?php if ($CurrentUser->priv < 2) { ?>
        <a href="/download">
          <div rounded=wide background=besure color=dark-orange ripple-effect posrel clickable>
            <div fl alic jucc gap=smol pblock24 pinline18>
              <mi mid>download</mi>
              <p text bold>Setup heia.kim</p>
            </div>
          </div>
        </a>
      <?php } ?>

      <!--- PREMIUM --->
      <?php if ($premium_time_left) { ?>
        <div pblock12>
          <p text smol bold timestamp ttup><?= APP_SETTING->premium_feature_name; ?></p>
        </div>

        <a href="/my/premium">
          <div rounded=wide ripple-effect filled hover=slight>
            <div fl fldirrow gap align-items=center pblock18 pinline18>
              <div fl align-items=center gap>
                <mi><?= PREMIUM_ICON; ?></mi>
                <p text std bold><?= $premium_time_left; ?></p>
              </div>
            </div>
          </div>
        </a>
      <?php } else { ?>
        <div data-action="popup:open" data-href="/user/buy-premium?id=<?= $CurrentUser->id; ?>" rounded=wide background=premium color=premium ripple-effect posrel clickable animation="premium" style=overflow:hidden;>
          <div fl alic jucc gap=smol pblock24 pinline18>
            <mi mid><?= PREMIUM_ICON; ?></mi>
            <p text><?= __("Unlock <strong>Premium+</strong>") ?></p>
          </div>
        </div>
      <?php } ?>
    </div>

    <!--- SQUAD --->
    <div fl fldircol gap=smol>
      <div pblock12>
        <p text smol bold timestamp ttup>Squad</p>
      </div>
      <?php if (!$CurrentUser->squad) { ?>
        <a href="/squads">
          <div rounded=wide filled hover=slight ripple-effect posrel>
            <div fl fldirrow gap alic pblock24 pinline18>
              <mi>workspaces</mi>
              <p text std bold trimt><?= __("Join a squad") ?></p>
            </div>
          </div>
        </a>
      <?php

      } else {
        $squad_headline = $CurrentUser->squad->headline ?? "default.jpg";
        $squad_logo = $CurrentUser->squad->logo;

      ?>
        <div fl alistretch gap=smol posrel>
          <a href="<?= "/squad/" . $CurrentUser->squad->id; ?>" style=flex:1;position:relative;z-index:1000;>
            <div rounded=wide ripple-effect clickable posrel style=overflow:hidden;>
              <div style="position:absolute;top:0;left:0;height:100%;
                    width:100%;background:url(<?= CLAN_IMAGE_URL . "/headline-images/" . $squad_headline; ?>) center no-repeat;background-size:cover;">
                <div style="position:absolute;top:0;left:0;height:100%;
                    width:100%;background:rgba(0,0,0,.42);">
                </div>
              </div>
              <div fl fldirrow gap align-items=center pblock18 pinline18 z>
                <div fl align-items=center gap>
                  <picture size=smol circled>
                    <?php include TEMPLATE . "/helper/squads/_image_logo.php"; ?>
                  </picture>
                  <div color=white>
                    <p text std bold trimt><?= $CurrentUser->squad->name; ?></p>
                    <p text smol><?= __("Go to your squad") ?></p>
                  </div>
                </div>
              </div>
            </div>
          </a>

          <a href="/manage/squad" dno>
            <div has-tooltip=bottom style="height:100%;padding-inline:1.2em;" background=slighter hover=slight rounded fl jucc alic ripple-effect>
              <i class="mi">tune</i>
              <div ttooltip>
                <p text std bold><?= __("Manage squad's settings") ?></p>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>

    <!--- PROFILE --->
    <div fl fldircol gap=smol>
      <div fl fldircol gap=smol>
        <div pblock12>
          <p text smol bold timestamp ttup><?= __("Profile") ?></p>
        </div>
        <div fl fldircol gap=smoler>
          <a href="<?= "/u/$CurrentUser->id"; ?>">
            <div rounded=wide ripple-effect filled hover=slight>
              <div fl fldirrow gap alic pblock18 pinline18>
                <mi>face</mi>
                <div>
                  <p text std bold><?= $CurrentUser->name(); ?></p>
                  <p text smol><?= __("Go to your profile") ?></p>
                </div>
              </div>
            </div>
          </a>
        </div>
      </div>

      <a href="/my/overview">
        <div rounded=wide filled hoverable ripple-effect posrel>
          <div fl fldirrow gap alic p18>
            <mi>settings</mi>
            <p text std bold trimt><?= __("Settings") ?></p>
          </div>
        </div>
      </a>

      <?php if ($CurrentUser->has_recommended_settings()) { ?>
        <a href="/my/recommendations">
          <div filled rounded=wide hoverable>
            <div p18 fl alic gap=smol+>
              <mi color=orange>error</mi>
              <p text>You have recommended settings for your account</p>
            </div>
          </div>
        </a>
      <?php } ?>
    </div>
  </div>
</user-menu>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
