<?php

use Bruder\Heiakim\Model\Squad;
use Bruder\Heiakim\Model\User;
use Bruder\Time\Time;

/**
 * @var User $CurrentUser
 * @var Squad $Squad
 */

?>

<!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, HEADER ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

<header full=squad scroll-manipulated>
  <div inner>
    <div w100 fl jucend hide-scrolled posrel>
      <mbutton hide-mobile material size=std has-icon=left
        <?= $Squad->joinable === 0 ? "background=unfollow color=dark-red" : ($Squad->joinable === 1 ? "background=besure color=dark-orange" : "background=follow color=dark-green"); ?>>
        <mi><?= $Squad->joinable === 0 ? "public_off" : ($Squad->joinable === 1 ? "vpn_lock" : "globe_asia"); ?></mi>
        <p text bold><?= $Squad->display_publicity(); ?></p>
      </mbutton>
      <mbutton show-mobile material size=std icon-only
        <?= $Squad->joinable === 0 ? "background=unfollow color=dark-red" : ($Squad->joinable === 1 ? "background=besure color=dark-orange" : "background=follow color=dark-green"); ?>>
        <mi><?= $Squad->joinable === 0 ? "public_off" : ($Squad->joinable === 1 ? "vpn_lock" : "globe_asia"); ?></mi>
      </mbutton>
    </div>

    <picture cover>
      <?php $Squad->headline(); ?>
    </picture>

    <picture image>
      <?php $Squad->logo(); ?>
    </picture>

    <div bottom-wrap>
      <div fl fldircol gap=smol>
        <div fl gap=smol+ alic tac>
          <div tag background=special color=light rounded ttup>
            <p text bold><?= $Squad->tag; ?></p>
          </div>
          <p name text bold><?= $Squad->name; ?></p>
        </div>
        <p text hide-scrolled>Created &middot; <span color=company><?= Time::ago($Squad->created_at, true) ?></span></p>
      </div>

      <?php

      /**
       * @var object
       */
      $placements = $Squad->placement();

      ?>

      <div placements fl alic gap>
        <p ttup slight text bold hide-tablet>Placement</p>
        <div fl alic jucc filled rounded=wide p12 style=height:51px; clickable>
          <?php if ($Squad->gumode_enabled(0)) { ?>
            <a href="/leaderboard/osu/vanilla/performance/squads" has-tooltip=bottom>
              <div style=min-width:4em; fl alic jucc gap=smol>
                <mi class="osu-icon osu-vanilla"></mi>
                <p text midler bold><?= $placements[0]->performance; ?></p>
              </div>
              <div ttooltip>
                Standart
              </div>
            </a>
          <?php } ?>

          <?php if ($Squad->gumode_enabled(1)) { ?>
            <div dot-divider></div>
            <a href="/leaderboard/ctb/vanilla/performance/squads" has-tooltip=bottom>
              <div style=min-width:4em; fl alic jucc gap=smol>
                <mi class="osu-icon osu-ctb"></mi>
                <p text midler bold><?= $placements[1]->performance; ?></p>
              </div>
              <div ttooltip>
                Catch the Beat
              </div>
            </a>
          <?php } ?>

          <?php if ($Squad->gumode_enabled(2)) { ?>
            <div dot-divider></div>
            <a href="/leaderboard/taiko/vanilla/performance/squads" has-tooltip=bottom>
              <div style=min-width:4em; fl alic jucc gap=smol>
                <mi class="osu-icon osu-taiko"></mi>
                <p text midler bold><?= $placements[2]->performance; ?></p>
              </div>
              <div ttooltip>
                Taiko
              </div>
            </a>
          <?php } ?>

          <?php if ($Squad->gumode_enabled(3)) { ?>
            <div dot-divider></div>
            <a href="/leaderboard/mania/vanilla/performance/squads" has-tooltip=bottom>
              <div style=min-width:4em; fl alic jucc gap=smol>
                <mi class="osu-icon osu-mania"></mi>
                <p text midler bold><?= $placements[3]->performance; ?></p>
              </div>
              <div ttooltip>
                Mania
              </div>
            </a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</header>

<!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, PAGE NAVIGATOR ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

<page-navigator>
  <div></div>

  <div pn-options>

    <?php switch ($CurrentUser->available_action_for($Squad)):
      case "is_member": ?>
        <a href="/manage/squad/leave">
          <mbutton pn-option material icon-only size=mid outlined has-tooltip=right>
            <mi>arrow_circle_left</mi>
            <div ttooltip>
              <p text bold><?= __("Leave") ?></p>
            </div>
          </mbutton>
        </a>
      <?php break;
      case "invite_pending": ?>
        <form request="/squad/invite/edit" reload responder=always method=POST>
          <input type=hidden name=id value=<?= $Invitation->id; ?> />
          <mbutton pn-option submit-closest animation=pulse material icon-only size=mid background=refollow color=dark-blue has-tooltip=right>
            <mi>done_all</mi>
            <div ttooltip>
              <p text bold>Accept invitation!</p>
            </div>
          </mbutton>
        </form>
      <?php break;
      case "request_pending": ?>
        <form request="squad:request:delete" reload responder=always>
          <input type=hidden name=id value=<?= $Request->id; ?> />
          <mbutton pn-option submit-closest animation=pulse material icon-only size=mid background=besure color=dark-orange has-tooltip=right>
            <mi>do_not_disturb_on</mi>
            <div ttooltip>
              <p text bold><?= __("Cancel request") ?>
              <p>
            </div>
          </mbutton>
        </form>
      <?php break;
      case "can_join": ?>
        <mbutton pn-option request-get="squad:request:new" data-id="<?= $Squad->id; ?>" material icon-only size=mid background=clean has-tooltip=right>
          <mi>add_circle</mi>
          <div ttooltip>
            <p text bold><?= __("Join") ?></p>
          </div>
        </mbutton>
      <?php break;
      case "can_request": ?>
        <mbutton pn-option request-get="squad:request:new" data-id="<?= $Squad->id; ?>" material icon-only size=mid outlined has-tooltip=right>
          <mi>arrow_circle_right</mi>
          <div ttooltip>
            <p text bold><?= __("Request membership") ?></p>
          </div>
        </mbutton>
      <?php break;
      case "login": ?>
        <a href="/register">
          <mbutton pn-option material icon-only size=mid outlined has-tooltip=right>
            <mi>login</mi>
            <div ttooltip>
              <p text bold><?= __("Sign up & join") ?></p>
            </div>
          </mbutton>
        </a>
    <?php break;
      default:
    endswitch ?>

    <div pn-option pn-o-divider></div>

    <a pn-option href="<?= $base_url; ?>">
      <mbutton material icon-only size=mid background=clean has-tooltip=right
        <?php display_active($page, ["index", "osu", "taiko", "mania", "ctb"]); ?>>
        <mi>browse</mi>
        <div ttooltip>
          <p text bold>Feed</p>
        </div>
      </mbutton>
    </a>

    <a pn-option href="<?= $base_url; ?>/community">
      <mbutton material icon-only size=mid background=clean has-tooltip=right
        <?php display_active($page, ["community"]); ?>>
        <mi>crowdsource</mi>
        <div ttooltip>
          <p text bold>Community</p>
        </div>
      </mbutton>
    </a>

    <?php if ($Squad->is($CurrentUser->squad)) { ?>
      <a pn-option href="<?= $base_url; ?>/threads" disabled>
        <mbutton material icon-only size=mid background=clean has-tooltip=right
          <?php display_active($page, ["threads", "thread"]); ?>>
          <mi>gesture</mi>
          <div ttooltip>
            <p text bold>Threads</p>
          </div>
        </mbutton>
      </a>

      <div pn-option pn-o-divider></div>

      <a pn-option href="/manage/squad">
        <mbutton manage size=mid icon-only material has-tooltip=right>
          <mi>settings</mi>
          <div ttooltip>
            <p text bold>Squad Manager</p>
          </div>
        </mbutton>
      </a>
    <?php } ?>
  </div>

  <a pn-option href="/">
    <mbutton filled material icon-only size=mid>
      <mi>arrow_back</mi>
    </mbutton>
  </a>
</page-navigator>

<!--- ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, COMPOSE BUTTON ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,, --->

<?php if ($CurrentUser->sqcan_take_action_in($Squad) && $CurrentUser->squad->is($Squad)) { ?>
  <mode-menu>
    <jump-menu mm-menu filled="lighter" elevated color="dynamic">
      <div jm-inr>
        <p dno text bold smol pinline4 pblock14 ttup slighter>Compose new</p>
        <div
          request-get="ui:squad:posting-machine"
          data-type=squad:post
          data-sub-type=text
          ripple-effect class="jm__option" hoverable>
          <mi>format_quote</mi>
          <p text>Text</p>
        </div>
        <div
          request-get="ui:squad:posting-machine"
          data-type=squad:post
          data-sub-type=poll
          ripple-effect class="jm__option" hoverable>
          <mi>ballot</mi>
          <p text>Poll</p>
        </div>
        <div disabled ripple-effect class="jm__option" hoverable>
          <mi>diversity_3</mi>
          <div>
            <p text>Group</p>
            <p text smoler>Not yet available</p>
          </div>
        </div>
      </div>
    </jump-menu>

    <mbutton mm-open size=wide has-icon=left elevated=mid material background=company color=light>
      <div mm-open-loading>
        <?php include COMPONENT . "/dot-loader.html"; ?>
      </div>
      <mi>add</mi>
      <div>
        <p text bold>Compose</p>
      </div>
    </mbutton>
  </mode-menu>
<?php } ?>