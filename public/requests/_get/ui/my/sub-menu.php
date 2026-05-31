<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Http\Request;

/**
 * @var Request $Request
 */

/**
 * @var string
 */
$sub = filter_input(INPUT_GET, "sub", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var string
 */
$section = filter_input(INPUT_GET, "section", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Begin output buffer.
 */
ob_start();

?>

<toggle-sub-menu>
  <mbutton icon-only size=mid material filled elevated>
    <i class="mi">menu</i>
  </mbutton>
</toggle-sub-menu>

<sub-menu animation=zoom-in-smol>
  <div class="sub_menu__inr">

    <!--- ACCOUNT --->
    <div class=sm__button_row>
      <a href="/home" mb>
        <div class=sm__button>
          <mi>reply_all</mi>
          <p class=b__name text std>Back to feed</p>
        </div>
      </a>

      <div class="sm__label">
        <p text smol bold ttup timestamp>Account</p>
      </div>

      <a href="/my/overview">
        <div class=sm__button <?php if ($sub == "overview" || !$sub) echo "active"; ?>>
          <p class=b__icon>
            <i class="mi" text mid>account_tree</i>
          </p>
          <p class=b__name text std><?= __("Overview") ?></p>
        </div>
      </a>

      <a href="/my/personal">
        <div class=sm__button <?php if ($sub == "personal") echo "active"; ?>>
          <p class=b__icon>
            <i class="mi" text mid>insert_emoticon</i>
          </p>
          <p class=b__name text std><?= __("Personals") ?></p>
        </div>
      </a>

      <a href="/my/game">
        <div class=sm__button <?php if ($sub == "game") echo "active"; ?>>
          <p class=b__icon>
            <i class="mi" text mid>extension</i>
          </p>
          <p class=b__name text std><?= __("Gameplay") ?></p>
        </div>
      </a>

      <a href="/my/privacy">
        <div class=sm__button <?php if ($sub == "privacy") echo "active"; ?>>
          <p class=b__icon>
            <i class="mi" text mid>shield_person</i>
          </p>
          <p class=b__name text std><?= __("Data & Privacy") ?></p>
        </div>
      </a>

      <a href="/my/security">
        <div class=sm__button <?php if ($sub == "security") echo "active"; ?>>
          <div class=b__icon>
            <i class="mi" text mid>vpn_key</i>
          </div>
          <p class=b__name text std><?= __("Security") ?></p>
        </div>
      </a>

      <a href="/my/website">
        <div class=sm__button <?php if ($sub == "website") echo "active"; ?>>
          <p class=b__icon>
            <i class="mi" text mid>view_carousel</i>
          </p>
          <p class=b__name text std><?= APP_NAME; ?></p>
        </div>
      </a>
    </div>


    <!--- PREMIUM --->
    <?php if ($CurrentUser->is_premium()) { ?>
      <div class=sm__featured outlined fl fldircol>
        <div class=sm__label hide-shrinked>
          <p text smol bold ttup timestamp><?= PREMIUM_NAME; ?></p>
        </div>

        <a href="/my/premium">
          <div class=sm__button <?php if ($sub == "premium") echo "active"; ?>>
            <p class=b__icon>
              <i class="mi" text mid><?= PREMIUM_ICON; ?></i>
            </p>
            <p class=b__name text std><?= __("Settings") ?></p>
          </div>
        </a>
      </div>
    <?php } ?>


    <!--- SQUAD --->
    <div class="sm__button_row">
      <div class=sm__label>
        <p text smol bold ttup timestamp>Squad</p>
      </div>

      <?php

      $Squad = $CurrentUser->squad;

      if (!$Squad) {

      ?>

        <a href="/squads">
          <div class=sm__button>
            <p class=b__icon>
              <i class="mi" text mid>add</i>
            </p>
            <p class=b__name text std><?= __("Find one") ?></p>
          </div>
        </a>

      <?php

      } else {

        $SquadUser = $CurrentUser->squad_user;

      ?>

        <a href="/squad/<?= $Squad->id; ?>">
          <div class=sm__button>
            <div class=b__icon>
              <picture style=height:1.72em;width:1.72em;>
                <?php $Squad->logo(); ?>
              </picture>
            </div>
            <p class=b__name text std><?= $Squad->name; ?></p>
          </div>
        </a>

        <a href="/manage/squad">
          <div class=sm__button <?php if ($section == "squad" && !in_array($sub, ["requests", "invites"])) echo "active"; ?>>
            <div class=b__icon>
              <i class="mi" text mid>settings</i>
            </div>
            <p class=b__name text std><?= __("Manage") ?></p>
          </div>
        </a>

        <?php

        if ($SquadUser->can("manage", "users")) {
          $SquadJoinRequests = $Squad->active_requests();
          $join_request_count = $SquadJoinRequests->count();

        ?>

          <a href="/manage/squad/requests">
            <div class=sm__button <?php if ($section == "squad" && in_array($sub, ["requests", "invites"])) echo "active"; ?> justify-content=space-between>
              <div class=b__icon>
                <i class=mi text mid>swap_horizontal_circle</i>
                <?php if ($join_request_count) { ?>
                  <div class=b__badge_label background=special fl justify-content=center align-items=center>
                    <p text style=font-size:.6em; semi-bold color=white><?= $join_request_count; ?></p>
                  </div>
                <?php } ?>
              </div>
              <p class=b__name text std><?= __("Pending requests") ?></p>
            </div>
          </a>

      <?php

        }
      }

      ?>

    </div>


  </div>
</sub-menu>

<?php

/**
 * * Success
 */
die($Request->success(data: ob_get_clean()));
