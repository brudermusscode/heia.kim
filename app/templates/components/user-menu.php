<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Heiakim\Application\Cookie;
use Heiakim\Model\Session;
use Heiakim\Model\Squad;
use Heiakim\Model\Squad\SquadUser;

/**
 * @var ?Squad $CurrentSquad
 * @var ?SquadUser $CurrentSquadUser
 */

ob_start(); ?>

<?php if (LOGGED) : ?>
  <user-menu>
    <div toggle fl alic jucc has-tooltip=top>
      <mi>keyboard_arrow_down</mi>
      <div ttooltip>Toggle menu</div>
    </div>

    <a href="<?= CurrentUser->link(); ?>">
      <mbutton midler has-image=left background=clean hoverable has-tooltip=top
        style="padding-right:12px;">
        <picture in-menu size=std circled>
          <?php CurrentUser->image(); ?>
        </picture>
        <?= CurrentUser->name() ?>
        <div ttooltip>Your Profile</div>
      </mbutton>
    </a>

    <div dot-divider></div>

    <mbutton midler icon-only background=clean hoverable has-tooltip=top
      open-ui-component=notifications
      url="/notification/category/all"
      toggle-user-menu="1800"
      <?= CurrentUser->unread_notifications_count() ? "has-notifications" : "" ?>>
      <mi></mi>
      <div ttooltip>Notifications</div>
    </mbutton>

    <mbutton midler icon-only background=clean hoverable has-tooltip=top
      open-ui-component="user-manager"
      url="/user-manager/overview"
      toggle-user-menu>
      <mi>settings</mi>
      <div ttooltip>Account Manager</div>
    </mbutton>

    <theme-switcher has-tooltip=top class="theme_switcher" <?= APP->dark_mode_enabled ? "active" : ""; ?>>
      <div class="theme_switcher__icon" left>
        <mi>dark_mode</mi>
      </div>
      <div class="theme_switcher__icon" right>
        <mi>light_mode</mi>
      </div>
      <div ttooltip><?php echo __("Switch color mode") ?></div>
    </theme-switcher>

    <?php

    if (CurrentUser->has_squad()) :

      /**
       * @var Squad CurrentUser->squad
       */

    ?>
      <div dot-divider></div>

      <a href="/squad/<?= $CurrentSquad->id; ?>">
        <mbutton midler image-only background=clean hoverable has-tooltip=top>
          <div rounded=smol slight pinline4 pblock1 background=invert color=invert z
            style="position:absolute;bottom:0;left:50%;translate:-50% 0;">
            <p text smol semibold><?= $CurrentSquad->tag ?></p>
          </div>
          <picture in-menu size=std circled>
            <?php $CurrentSquad->logo(); ?>
          </picture>
          <div ttooltip><?= $CurrentSquad->name ?></div>
        </mbutton>
      </a>
    <?php endif; ?>

    <div dot-divider></div>

    <form data-form="session:delete">
      <input type=hidden name=token value="<?= Cookie::get(Session::$persistent_cookies[1]); ?>" />
      <mbutton midler submit-closest icon-only hoverable has-tooltip=top>
        <mi>logout</mi>
        <div ttooltip><?= __("Logout"); ?></div>
      </mbutton>
    </form>
  </user-menu>


  <ui-component type=notifications>
    <div loading-overlay>
      <div material-bar-loader class="linear-progress-material">
        <div class="bar bar1"></div>
        <div class="bar bar2"></div>
      </div>
    </div>

    <nc-inr>
      <box-model rounded="wide" filled p62 fl fldircol alic jucc gap style="height:100%;" flexone>
        <div style="height:4.2em;width:4.2em;" fl alic jucc circled filled=darker>
          <mi wide>downloading</mi>
        </div>
        <div tac>
          <p text bold mid>Wait a second</p>
          <p text>We are loading your notifications</p>
        </div>
      </box-model>
    </nc-inr>

    <nc-tabs-floating hide-mobile
      data-action="notification:category">
      <nc-tab-option data-category=all>
        <mi>all_inclusive</mi>
        <p text>All</p>
      </nc-tab-option>

      <nc-tab-option data-category=social>
        <mi>interests</mi>
        <p text>Social</p>
      </nc-tab-option>

      <nc-tab-option data-category="squad">
        <mi>workspaces</mi>
        <p text>Squad</p>
      </nc-tab-option>

      <nc-tab-option data-category="reports">
        <mi>campaign</mi>
        <p text>Reports</p>
      </nc-tab-option>

      <nc-tab-option data-category="system">
        <mi>browse_activity</mi>
        <p text>System</p>
      </nc-tab-option>
    </nc-tabs-floating>

    <nc-tabs show-mobile>
      <nc-tab-option active>
        <mi>all_inclusive</mi>
        <p text>All</p>
      </nc-tab-option>

      <nc-tab-option>
        <mi>interests</mi>
        <p text>Follows</p>
      </nc-tab-option>

      <nc-tab-option>
        <mi>workspaces</mi>
        <p text>Squad</p>
      </nc-tab-option>

      <nc-tab-option>
        <mi>browse_activity</mi>
        <p text>System</p>
      </nc-tab-option>
    </nc-tabs>
  </ui-component>


  <ui-component type=user-manager>
    <div loading-overlay>
      <div material-bar-loader class="linear-progress-material">
        <div class="bar bar1"></div>
        <div class="bar bar2"></div>
      </div>
    </div>

    <nc-inr>
      <box-model rounded="wide" background=transparent p62 fl fldircol alic jucc gap style="height:100%;" flexone>
        <div dno style="height:4.2em;width:4.2em;" fl alic jucc circled filled=darker>
          <mi wide>downloading</mi>
        </div>

        <dotlottie-wc
          src="https://lottie.host/540f0e28-590a-4eaf-adb5-900b30b91e8f/M2HiBpksQx.lottie"
          style="width: 180px;height: 180px;margin-bottom:-1.2em;"
          autoplay
          loop></dotlottie-wc>

        <div tac>
          <p text bold mid>Wait a second</p>
          <p text>We are loading your settings</p>
        </div>
      </box-model>
    </nc-inr>

    <nc-tabs-floating hide-mobile>
      <nc-tab-option open=overview>
        <mi>dashboard</mi>
        <p text>Overview</p>
      </nc-tab-option>

      <nc-tab-option open=personal>
        <mi>insert_emoticon</mi>
        <p text>Personals</p>
      </nc-tab-option>

      <nc-tab-option open=game>
        <mi>extension</mi>
        <p text>Gameplay</p>
      </nc-tab-option>

      <nc-tab-option open=privacy>
        <mi>shield_person</mi>
        <p text>Data & Privacy</p>
      </nc-tab-option>

      <nc-tab-option open=security>
        <mi>vpn_key</mi>
        <p text>Security</p>
      </nc-tab-option>

      <nc-tab-option open=website>
        <mi>desktop_mac</mi>
        <p text>Appearance</p>
      </nc-tab-option>
    </nc-tabs-floating>
  </ui-component>
<?php endif;

request_success(data: ob_get_clean(), die: true);
