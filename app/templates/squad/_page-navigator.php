<?php

use Heiakim\Model\Squad\SquadRequest;

/**
 * @var string $id
 * @var string $sub
 * @var string $page
 * @var SquadRequest $Invitation
 * @var SquadRequest $Request
 */

?>

<page-navigator>
  <div></div>

  <div pn-options>

    <?php switch (CurrentUser->available_action_for($Squad)):
      case "is_member": ?>
        <a href="/manage/squad/leave">
          <mbutton mid pn-option icon-only outlined has-tooltip=right>
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
          <mbutton mid pn-option submit-closest animation=pulse icon-only background=refollow color=dark-blue has-tooltip=right>
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
          <mbutton pn-option submit-closest animation=pulse icon-only background=besure color=dark-orange has-tooltip=right>
            <mi>do_not_disturb_on</mi>
            <div ttooltip>
              <p text bold><?= __("Cancel request") ?>
              <p>
            </div>
          </mbutton>
        </form>
      <?php break;
      case "can_join": ?>
        <mbutton mid pn-option icon-only background=clean has-tooltip=right
          request-get="squad:request:new"
          data-id="<?= $Squad->id; ?>">
          <mi>add_circle</mi>
          <div ttooltip>
            <p text bold><?= __("Join") ?></p>
          </div>
        </mbutton>
      <?php break;
      case "can_request": ?>
        <mbutton mid pn-option icon-only mid outlined has-tooltip=right
          request-get="squad:request:new"
          data-id="<?= $Squad->id; ?>">
          <mi>arrow_circle_right</mi>
          <div ttooltip>
            <p text bold><?= __("Request membership") ?></p>
          </div>
        </mbutton>
      <?php break;
      case "login": ?>
        <a href="/register">
          <mbutton mid pn-option icon-only outlined has-tooltip=right>
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
      <mbutton mid icon-only background=clean has-tooltip=right
        <?php display_active($page, ["index", "osu", "taiko", "mania", "ctb"]); ?>>
        <mi>browse</mi>
        <div ttooltip>
          <p text bold>Feed</p>
        </div>
      </mbutton>
    </a>

    <a pn-option href="<?= $base_url; ?>/community">
      <mbutton mid icon-only background=clean has-tooltip=right
        <?php display_active($page, ["community"]); ?>>
        <mi>crowdsource</mi>
        <div ttooltip>
          <p text bold>Community</p>
        </div>
      </mbutton>
    </a>

    <?php if ($Squad->is(CurrentUser->squad)) { ?>
      <a pn-option href="<?= $base_url; ?>/threads" disabled>
        <mbutton mid icon-only background=clean has-tooltip=right
          <?php display_active($page, ["threads", "thread"]); ?>>
          <mi>gesture</mi>
          <div ttooltip>
            <p text bold>Threads</p>
          </div>
        </mbutton>
      </a>

      <div pn-option pn-o-divider></div>

      <a pn-option href="/manage/squad">
        <mbutton mid manage icon-only has-tooltip=right>
          <mi>settings</mi>
          <div ttooltip>
            <p text bold>Squad Manager</p>
          </div>
        </mbutton>
      </a>
    <?php } ?>
  </div>

  <a pn-option href="/">
    <mbutton mid filled icon-only>
      <mi>arrow_back</mi>
    </mbutton>
  </a>
</page-navigator>