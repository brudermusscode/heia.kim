<?php

use Heiakim\Model\Squad\SquadUser;

$title_w_desc = [
  "overview" => [
    __("Hey") . ", " . CurrentUser->name,
    __("Welcome to the account manager"),
    "dashboard"
  ],
  "personal" => [
    __("Personal Information"),
    __("You and your profile"),
    "insert_emoticon"
  ],
  "game" => [
    __("Gameplay"),
    __("Your journey and playing preferences"),
    "extension"
  ],
  "privacy" => [
    __("Data & Privacy"),
    __("Manage your data and what other players see"),
    "shield_person"
  ],
  "security" => [
    __("Security"),
    __("Secure your account from third party access"),
    "vpn_key"
  ],
  "website" => [
    "Appearance",
    __("Increase your comfort by adjusting the website to your needs"),
    "desktop_mac"
  ],
];

?>

<?php if (!$sub) : ?>
  <div filled=lighter pt42 pb12 style=position:sticky;top:0;margin-bottom:-32px; z fl alic gap=smol+>
    <div filled=darker color=active-text fl alic jucc style="height:56px;width:56px;" circled>
      <mi><?= $title_w_desc[$category][2] ?></mi>
    </div>

    <div>
      <p text bold mid><?= $title_w_desc[$category][0] ?></p>
      <p text slight><?= $title_w_desc[$category][1] ?></p>
    </div>
  </div>
<?php endif; ?>




<toggle-sub-menu dno>
  <mbutton icon-only size=mid material filled elevated>
    <i class="mi">menu</i>
  </mbutton>
</toggle-sub-menu>

<sub-menu dno>
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

      <a href="/my/overview" sub <?php if ($sub == "overview" || !$sub) echo "active"; ?>>
        <div class=sm__button>
          <p class=b__icon>
            <mi>dashboard</mi>
          </p>
          <p class=b__name text std><?= __("Overview") ?></p>
        </div>
      </a>

      <a href="/my/personal" sub <?php if ($sub == "personal") echo "active"; ?>>
        <div class=sm__button>
          <p class=b__icon>
            <mi>insert_emoticon</mi>
          </p>
          <p class=b__name text std><?= __("Personals") ?></p>
        </div>
      </a>

      <a href="/my/game" sub <?php if ($sub == "game") echo "active"; ?>>
        <div class=sm__button>
          <p class=b__icon>
            <mi>extension</mi>
          </p>
          <p class=b__name text std><?= __("Gameplay") ?></p>
        </div>
      </a>

      <a href="/my/privacy" sub <?php if ($sub == "privacy") echo "active"; ?>>
        <div class=sm__button>
          <p class=b__icon>
            <mi>shield_person</mi>
          </p>
          <p class=b__name text std><?= __("Data & Privacy") ?></p>
        </div>
      </a>

      <a href="/my/security" sub <?php if ($sub == "security") echo "active"; ?>>
        <div class=sm__button>
          <div class=b__icon>
            <mi>vpn_key</mi>
          </div>
          <p class=b__name text std><?= __("Security") ?></p>
        </div>
      </a>

      <a href="/my/website" sub <?php if ($sub == "website") echo "active"; ?>>
        <div class=sm__button>
          <p class=b__icon>
            <mi>desktop_mac</mi>
          </p>
          <p class=b__name text std>Appearance</p>
        </div>
      </a>
    </div>


    <!--- PREMIUM --->
    <?php if (CurrentUser->is_premium()) { ?>
      <div class=sm__featured outlined fl fldircol>
        <div class=sm__label hide-shrinked>
          <p text smol bold ttup timestamp><?= PREMIUM_NAME; ?></p>
        </div>

        <a href="/my/premium" sub>
          <div class=sm__button <?php if ($sub == "premium") echo "active"; ?>>
            <p class=b__icon>
              <mi><?= PREMIUM_ICON; ?></mi>
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

      $Squad = CurrentUser->squad;

      if (!$Squad) {

      ?>

        <a href="/squads">
          <div class=sm__button>
            <p class=b__icon>
              <mi>add</mi>
            </p>
            <p class=b__name text std><?= __("Find one") ?></p>
          </div>
        </a>

      <?php

      } else {

        /**
         * @var ?SquadUser
         */
        $SquadUser = CurrentUser->squad_user;

      ?>

        <a href="/manage/squad" sub <?php if ($section == "squad" && !in_array($sub, ["requests", "invites"])) echo "active"; ?>>
          <div class=sm__button>
            <div class=b__icon>
              <mi>settings</mi>
            </div>
            <p class=b__name text std><?= __("Manage") ?></p>
          </div>
        </a>

        <?php

        /**
         * Only show, if the current user has privileges to
         * coordinate members in their squad.
         */
        if ($SquadUser->can("coordinate", "users")) {

          $SquadJoinRequests = $Squad->active_requests();
          $join_request_count = $SquadJoinRequests->count();

        ?>

          <a href="/manage/squad/requests" sub <?php if ($section == "squad" && in_array($sub, ["requests", "invites"])) echo "active"; ?>>
            <div class=sm__button justify-content=space-between>
              <div class=b__icon>
                <mi>swap_horizontal_circle</mi>
                <?php if ($join_request_count) { ?>
                  <div class=b__badge_label background=special fl justify-content=center align-items=center>
                    <p text style=font-size:.6em; semi-bold color=white><?= $join_request_count; ?></p>
                  </div>
                <?php } ?>
              </div>
              <p class=b__name text std><?= __("Pending requests") ?></p>
            </div>
          </a>

        <?php } ?>

        <a href="/squad/<?= $Squad->id; ?>">
          <div class=sm__button>
            <div class=b__icon>
              <picture style=height:1.72em;width:1.72em;>
                <?php $Squad->logo(); ?>
              </picture>
            </div>
            <p class=b__name text std trimt><?= $Squad->name; ?></p>
            <mi>arrow_forward</mi>
          </div>
        </a>

      <?php } ?>

    </div>


  </div>
</sub-menu>