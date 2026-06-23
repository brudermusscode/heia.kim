<?php

use Heiakim\Model\Squad;
use Heiakim\Time\Time;
use Heiakim\Model\Squad\SquadUser;
use Heiakim\Model\Squad\SquadFeedItem;

/**
 * @var Squad $Squad
 * @var SquadUser $SquadUser
 */

$publicity_text = CurrentUser->squad->joinable === 0
  ? "Private"
  : (CurrentUser->squad->joinable === 1
    ?  "Request only"
    : "Public for all");

?>

<content midler fl fldircol gap minlineauto>

  <?php if ($SquadUser->can("manage", "squad")) : ?>
    <div fl fldircol gap=smol+>
      <div grid-repeat=smol gap=smol>
        <form data-form="squad:update:image" style=flex:1;>
          <a data-action="squad:imagery" select-choose-file>
            <div p26 ripple-effect clickable filled=lighter rounded=mid fl gap alic jucsb>
              <div fl gap alic>
                <picture midplus circled>
                  <?php $Squad->logo(); ?>
                </picture>
                <p text bold midler>Logo</p>
              </div>
              <mi midler color=company>arrow_selector_tool</mi>
            </div>
          </a>

          <input type=hidden name=image_type value="logo" />
          <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
          <input trigger="file-input:change,go" type="file" name="image" size="32" accept="image/*" hidden />
        </form>

        <form data-form="squad:update:image" style=flex:1;>
          <a data-action="squad:imagery" select-choose-file data-type="headline">
            <box-model p48 ripple-effect clickable filled rounded=mid ovhid>
              <div z color=white fl alic jucsb>
                <div fl gap alic>
                  <mi wide>wallpaper</mi>
                  <p text bold midler>Headline</p>
                </div>
                <mi midler color=company>arrow_selector_tool</mi>
              </div>
              <div posabs ovhide h100 w100 style="top:0;left:0;z-index:0;">
                <div h100 w100 posabs z background=hover style="top:0;left:0;"></div>
                <picture size=full posabs>
                  <?php $Squad->headline(); ?>
                </picture>
              </div>
            </box-model>
          </a>

          <input type=hidden name=image_type value="headline" />
          <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
          <input trigger="file-input:change,go" type="file" name="image" size="32" accept="image/*" hidden />
        </form>
      </div>

      <div outlined p24 rounded fl alic gap=smol+ slight>
        <mi std>info</mi>
        <p text>Choosing an image automatically uploads it, so choose wisely!</p>
      </div>
    </div>
  <?php endif; ?>

  <?php if (
    $SquadUser->can("manage", "squad")
    || $SquadUser->can("manage", "users")
    || $SquadUser->can("coordinate", "users")
  ) : ?>
    <div filled=lighter rounded=wide p24>

      <?php if ($SquadUser->can("manage", "squad")) : ?>
        <a sub href="/manage/squad/name">
          <div hoverable p12 pr18 rounded=mid fl gap alic jucsb>
            <div fl alic gap>
              <mi mid style="width:48px;">format_size</mi>
              <div>
                <div fl gap=smol alic>
                  <p filled=darker pinline8 pblock4 rounded=std text bold smol>
                    <?= CurrentUser->squad->tag; ?>
                  </p>
                  <p rounded=std text bold std>
                    <?= CurrentUser->squad->name; ?>
                  </p>
                </div>
                <p text std>Name & Tag</p>
              </div>
            </div>
            <mi midler>east</mi>
          </div>
        </a>

        <divide horiz mblock8 minline18></divide>

        <a sub href="/manage/squad/modes">
          <div hoverable p12 pr18 rounded=mid fl gap alic jucsb>
            <div fl alic gap>
              <mi mid style="width:48px;">mode_standby</mi>
              <div>
                <p text std bold>Modes</p>
                <p text std>Relevant modes for your squad</p>
              </div>
            </div>
            <mi midler>east</mi>
          </div>
        </a>

        <divide horiz mblock8 minline18></divide>
      <?php endif; ?>

      <?php

      # Members that can coordinate other members as well as any rank above this can
      # enter the members tab. Inside there, the methods to manage the members are li-
      # mited to what the actual rank can do.
      if ($SquadUser->can("coordinate", "users")) : ?>
        <a href="/manage/squad/members">
          <div hoverable p12 pr18 rounded=mid fl gap alic jucsb>
            <div fl alic gap>
              <mi mid style="width:48px;">diversity_3</mi>
              <div>
                <p text std bold>
                  Members &middot;
                  <span color=company>
                    <?= CurrentUser->squad->members_count() ?></span>
                </p>
                <p text std>Manage members and their status</p>
              </div>
            </div>
            <div posrel>
              <mi midler>east</mi>
              <div notification-dot="" style="top:-4px;right:-4px;"></div>
            </div>
          </div>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>


  <?php if ($SquadUser->can("manage", "squad")) { ?>
    <div fl fldircol gap=smol+>
      <p text mid bold title-inline>Privacy</p>

      <div grid-repeat=smol gap=smol>
        <box-model grid-keeper outlined p18>
          <div p12>
            <p text midler bold>Publicity</p>
            <p text std>Restrict new members to join your squad</p>
          </div>

          <a sub href="/manage/squad/visibility">
            <div hoverable p12 rounded=mid fl gap alic jucsb>
              <div fl gap align-items=center>
                <mi mid>
                  <?php

                  echo
                  CurrentUser->squad->joinable === 0
                    ? "remove_circle"
                    : (CurrentUser->squad->joinable === 1
                      ?  "arrow_circle_right"
                      : "check_circle");

                  ?>
                </mi>
                <p text std><?= $publicity_text; ?></p>
              </div>
              <mi midler>east</mi>
            </div>
          </a>
        </box-model>

        <box-model grid-keeper outlined p18>
          <div p12>
            <p text midler bold>Logs</p>
            <p text std>See, what happens in your squad</p>
          </div>

          <?php

          /**
           * @var ?SquadFeedItem
           */
          $LastLog = $Squad->logs()
            ->whereNot("type", "__post__")
            ->latest()
            ->first();

          if ($LastLog) : ?>

            <a sub href="<?= "$base_url/logs"; ?>">
              <div hoverable p12 rounded=mid fl gap alic jucsb>
                <div fl gap=smol+ alic>
                  <picture std circled>
                    <?php $LastLog->user->image(); ?>
                  </picture>
                  <div>
                    <p text std bold><?= $LastLog->user->name(); ?></p>
                    <p text smol trimt><?= $LastLog->display_type()->append_text . " " . (!$LastLog->affected_user?->is($LastLog->user) ? $LastLog->affected_user?->name() : ""); ?> &middot; <span color=company><?= Time::ago($LastLog?->created_at, true); ?></p>
                  </div>
                </div>
                <div posrel>
                  <mi midler>east</mi>
                  <div notification-dot="" style="top:-4px;right:-4px;"></div>
                </div>
              </div>
            </a>

          <?php else : ?>

            <div fl alic gap=smol+ p12 slighter>
              <mi>change_history</mi>
              <p text>Nothing</p>
            </div>

          <?php endif; ?>
        </box-model>
      </div>
    </div>
  <?php } ?>


  <div fl fldircol gap=smol+>
    <p text mid bold title-inline>Others</p>

    <div filled=lighter p24 rounded=wide>
      <a href="/manage/squad/leave">
        <div hoverable p12 pr18 rounded=mid fl gap alic jucsb>
          <div fl gap alic>
            <mi style="width:48px;" mid>logout</mi>
            <div>
              <p text std bold>Leave <?= $Squad->name ?></p>
              <p text std>Get out of here</p>
            </div>
          </div>

          <mi midler>east</mi>
        </div>
      </a>
    </div>
  </div>

</content>