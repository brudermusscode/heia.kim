<?php

use Heiakim\Model\Score;
use Heiakim\Model\Artist;
use Heiakim\Model\Beatmap;
use Heiakim\Model\Gamemode;

/**
 * @var Score $Score
 * @var Beatmap $Beatmap
 * @var Artist $Artists
 */

/**
 * @var int
 */
$current_score_gumode = $Score->mode;

/**
 * @var object
 */
$mode_mod = Gamemode::gumode_text($current_score_gumode);

/**
 * @var bool
 */
$is_my_score ??= $Score->user->is(CurrentUser);

/**
 * @var bool
 */
$hide_reactions ??= true;

/**
 * @var bool
 */
$is_post ??= false;

/**
 * @var bool
 */
$can_interact_with_squad =
  ($sub ?? null) !== "thread"
  && (
    ($is_my_score && CurrentUser->squad)
    || (!$is_my_score && $Score->user->squad && $Score->user->squad->is_member(CurrentUser))
  );

?>

<div hover-menu menu-outer elevated=min data-react="reactions:open">
  <div class="options_inr">

    <?php if (!LOGGED) { ?>

      <a href="/login" style=margin-right:4px;>
        <div ripple-effect class=option background=follow color=dark-green>
          <div class=option_inr fl gap=smol alic>
            <mi>login</mi>
            <p text smol bold style=line-height:1.2em;>Login &nbsp; </p>
          </div>
        </div>
      </a>

    <?php } else { ?>

      <?php if (!$hide_reactions) { ?>
        <form data-form="ui:reactions" data-url="/ui/reactions?reference_id=<?= $Score->id; ?>&reaction_type=score">
          <div has-tooltip=top>
            <div submit-closest ripple-effect class=option>
              <div class=option_inr>
                <mi>add_reaction</mi>
              </div>
            </div>
            <div ttooltip>
              <p text std bold>Add Reaction</p>
            </div>
          </div>
        </form>
      <?php } ?>

    <?php } ?>

    <div has-tooltip=top>
      <a sub href='<?= "/beatmap-set/$Beatmap->set_id/$Beatmap->id/$mode_mod->mode/$mode_mod->mod"; ?>'>
        <div ripple-effect class=option>
          <div class=option_inr>
            <mi>web_stories</mi>
          </div>
        </div>
      </a>
      <div ttooltip>
        <p text std bold>View Beatmap</p>
      </div>
    </div>

    <?php if (LOGGED && $Score->is_submitted()) { ?>
      <a extern href="<?= _env("REPLAY_URL") . "/$Score->id"; ?>">
        <div has-tooltip=top>
          <div ripple-effect class=option>
            <div class=option_inr>
              <mi>download</mi>
            </div>
          </div>
          <div ttooltip>
            <p text std bold>Download Replay</p>
          </div>
        </div>
      </a>
    <?php } ?>

    <?php if (CurrentUser->squad && CurrentUser->sqcan_take_action_in(CurrentUser->squad) && !$is_post) { ?>
      <div overlay has-tooltip=top
        request-get="ui:squad:posting-machine"
        data-type=squad:post
        data-sub-type=text
        data-attachment-id=<?= $Score->id; ?>
        data-attachment-type=score>
        <div ripple-effect class=option>
          <div class=option_inr>
            <div notification-dot></div>
            <mi>post_add</mi>
          </div>
        </div>
        <div ttooltip>
          <p text std bold>Post to Squad</p>
        </div>
      </div>
    <?php } ?>

    <?php if (LOGGED) { ?>
      <div has-tooltip=top open-more-menu>
        <div ripple-effect class=option>
          <div class=option_inr>
            <mi>more_vert</mi>
          </div>
        </div>
        <div ttooltip>
          <p text std bold>More options</p>
        </div>
      </div>
    <?php } ?>
  </div>

  <?php if (LOGGED) { ?>
    <jump-menu menu-more filled=lighter elevated color=dynamic>

      <?php if (CURRENT_PAGE !== "score" && $Score?->id !== GET("id") && 1 === 2) : ?>
        <a href="/score/<?= $Score->id; ?>">
          <div submit-closest ripple-effect class=jm__option hoverable>
            <mi>whatshot</mi>
            <p text std>View Score</p>
          </div>
        </a>

        <div divide=line></div>
      <?php endif; ?>



      <!--- SQUAD --->
      <?php if ($can_interact_with_squad && 1 === 2) { ?>
        <div data-action="popup:open" data-href="/squad/thread/new?id=<?= $Score->id; ?>&type=score" ripple-effect class=jm__option hoverable>
          <mi>gesture</mi>
          <p text std>Create Squad Thread</p>
        </div>

        <div divide=line></div>
      <?php } ?>


      <!--- BEATMAP --->
      <a extern href="<?= _env("BEATMAP_MIRROR") . "/api/d/$Beatmap->set_id"; ?>">
        <div submit-closest ripple-effect class=jm__option hoverable>
          <mi>download</mi>
          <p text std>Download Beatmap</p>
        </div>
      </a>


      <!--- ARTIST --->
      <?php if ($Artists->first()) { ?>
        <div divide=line></div>

        <a href="<?= "/artist/" . $Artists->first()->id; ?>">
          <div submit-closest ripple-effect class=jm__option hoverable>
            <mi>artist</mi>
            <p text std>View Artist</p>
          </div>
        </a>
      <?php } ?>


      <?php if ($is_my_score) { ?>
        <div divide=line></div>

        <!--- Pin to profile --->
        <form request="user:pin:<?= CurrentUser->has_pinned($Score) ? "delete" : "create" ?>" reload responder>
          <input type=hidden name=id value=<?= $Score->id; ?> />
          <input type=hidden name=type value=score />
          <div submit-closest ripple-effect class=jm__option hoverable>
            <mi><?= CurrentUser->has_pinned($Score) ? 'link_off' : 'add_link'; ?></mi>
            <p text std><?= CurrentUser->has_pinned($Score) ? 'Unpin from' : 'Pin to'; ?> profile</p>
          </div>
        </form>

        <!--- PREMIUM: Set headline --->
        <?php if (CurrentUser->is_premium()) { ?>
          <?php if (CurrentUser->settings->headline != $Score->beatmap->set_id) { ?>
            <form data-form="users:settings,premium,edit" responder>
              <input type=hidden name=headline value=<?= $Score->beatmap->set_id; ?> />
              <div submit-closest ripple-effect class=jm__option hoverable>
                <mi>texture_add</mi>
                <p text std>Set as headline</p>
              </div>
            </form>
          <?php } ?>
        <?php } else { ?>
          <a href="/unlock/premium">
            <div ripple-effect class=jm__option hoverable background=premium color=premium>
              <mi><?= PREMIUM_ICON; ?></mi>
              <p text std>Set as headline</p>
            </div>
          </a>
        <?php } ?>
      <?php } ?>



      <?php if (!$is_my_score) { ?>
        <div divide=line></div>

        <?php

        $Report = CurrentUser->reports()
          ->where("reference_id", $Score->id)
          ->where("report_type", "score")
          ->count();

        if (!$Report) { ?>
          <a data-action="popup:open" data-href="<?= "/report/new?id=$Score->id&type=score"; ?>">
            <div ripple-effect class=jm__option hoverable color=red>
              <mi>campaign</mi>
              <p text std>Report</p>
            </div>
          </a>
        <?php } else { ?>
          <a disabled>
            <div ripple-effect class=jm__option hoverable>
              <mi>campaign</mi>
              <p text std>Report in progress</p>
            </div>
          </a>
        <?php } ?>
      <?php } ?>
    </jump-menu>
  <?php } ?>
</div>