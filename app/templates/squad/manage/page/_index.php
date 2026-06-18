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

<div content-width=pre-std>

  <div mb=wide tac>
    <p text bold wide>Squad</p>
    <p text std>Customize the uniqueness of your squad</p>
  </div>

  <?php if ($SquadUser->can("manage", "squad")) : ?>
    <div grid-repeat=smol gap=smol mb=smol>
      <form data-form="squad:update:image" style=flex:1;>
        <a data-action="squad:imagery" select-choose-file>
          <box-model ripple-effect clickable filled rounded=mid>
            <div p40 fl gap alic jucsb>
              <div fl gap alic>
                <picture size=mid circled>
                  <?php $Squad->logo(); ?>
                </picture>
                <p text bold midler>Logo</p>
              </div>
              <mi midler>edit</mi>
            </div>
          </box-model>
        </a>

        <input type=hidden name=image_type value="logo" />
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
        <input trigger="file-input:change,go" type="file" name="image" size="32" accept="image/*" hidden />
      </form>

      <form data-form="squad:update:image" style=flex:1;>
        <a data-action="squad:imagery" select-choose-file data-type="headline">
          <box-model ripple-effect clickable filled rounded=mid>
            <div p48 z color=white fl alic jucsb>
              <div fl gap alic>
                <mi wide>wallpaper</mi>
                <p text bold midler>Headline</p>
              </div>
              <mi midler>edit</mi>
            </div>
            <div rounded=mid style="position:absolute;top:0;left:0;height:100%;width:100%;z-index:0;overflow:hidden;">
              <div style="height:100%;width:100%;position:absolute;top:0;left:0;background:rgba(0,0,0,.42);z-index:1;">
              </div>
              <picture size=full style=position:absolute;z-index:0;>
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

    <div fl alistart gap=smol slight mb32 mt12 title-inline>
      <mi std mt2>info</mi>
      <p text>Choosing an image through the file explorer of your operating system will automatically upload it to your squad.</p>
    </div>

  <?php endif; ?>

  <?php

  /**
   * This whole section is just shown if the current squad user can
   * atleast coordinate users.
   */
  if ($SquadUser->can("coordinate", "users")) : ?>
    <box-model filled mb>
      <bm-inr size=mid>

        <?php

        /**
         * ? Managing Squad Appearance.
         */
        if ($SquadUser->can("manage", "squad")) : ?>
          <a sub href="/manage/squad/name">
            <div hoverable p12 style="padding-right:32px;" rounded=mid>
              <div fl gap align-items=center justify-content=space-between>
                <div fl gap align-items=center>
                  <div style="width:3.2em;" fl jucc alic>
                    <mi mid>format_size</mi>
                  </div>
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
            </div>
          </a>

          <div mt=smol mb=smol style="height:1px;width:calc(100% - 2.4em);margin-inline:1.2em;" filled=darker></div>

          <a sub href="/manage/squad/modes">
            <div hoverable p12 style="padding-right:32px;" rounded=mid>
              <div fl gap align-items=center justify-content=space-between>
                <div fl gap align-items=center>
                  <div style="width:3.2em;" fl jucc alic>
                    <mi mid>mode_standby</mi>
                  </div>

                  <div>
                    <p text std bold>Modes</p>
                    <p text std>Relevant modes for your squad</p>
                  </div>
                </div>

                <mi midler>east</mi>
              </div>
            </div>
          </a>

          <div mt=smol mb=smol style="height:1px;width:calc(100% - 2.4em);margin-inline:1.2em;" filled=darker></div>
        <?php endif; ?>

        <?php

        /**
         * ? Coordinating Members.
         */
        if ($SquadUser->can("coordinate", "users")) : ?>
          <a href="/manage/squad/members">
            <div hoverable p12 style="padding-right:32px;" rounded=mid>
              <div fl gap align-items=center justify-content=space-between>
                <div fl gap align-items=center>
                  <div style="width:3.2em;" fl justify-content=center align-items=center>
                    <mi mid>groups</mi>
                  </div>
                  <div>
                    <p text std bold>
                      Members &middot; <span color=company><?= CurrentUser->squad->members_count() ?></span>
                    </p>
                    <p text std>Manage members and their status</p>
                  </div>
                </div>
                <div posrel>
                  <mi midler>east</mi>
                  <div notification-dot="" style="top:-4px;right:-4px;"></div>
                </div>
              </div>
            </div>
          </a>
        <?php endif; ?>
      </bm-inr>
    </box-model>
  <?php endif; ?>


  <?php

  /**
   * ? Squad's Privacy => Chief only.
   */
  if ($SquadUser->can("manage", "squad")) { ?>
    <div title-inline mt=wide mb fl fldircol style="gap:.2em;">
      <p text midler bold>Privacy</p>
    </div>

    <div grid-repeat=smol gap=smol>
      <box-model grid-keeper outlined>
        <div p24>
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
        </div>
      </box-model>

      <box-model grid-keeper outlined>
        <div p24>
          <div p12>
            <p text midler bold>Logs</p>
            <p text std>See, what happens in your squad</p>
          </div>

          <?php

          /**
           * @var ?SquadFeedItem
           */
          $LastLog = $Squad
            ->logs()
            ->whereNot("type", "__post__")
            ->latest()
            ->first();

          if ($LastLog) :

          ?>

            <a sub href="<?= "$base_url/logs"; ?>">
              <div hoverable p12 rounded=mid fl gap alic jucsb>
                <div fl gap=smol+ alic>
                  <picture size=std circled>
                    <?php $LastLog->user->image(); ?>
                  </picture>
                  <div>
                    <p text std bold trimt><?= $LastLog->user->name(); ?></p>
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

        </div>
      </box-model>
    </div>
  <?php } ?>

  <div title-inline mt=wide mb>
    <p text midler bold>Advanced options</p>
  </div>

  <box-model filled mb>
    <div p24>
      <a href="/manage/squad/leave">
        <div hoverable p12 style="padding-right:32px;" rounded=mid>
          <div fl gap align-items=center justify-content=space-between>
            <div fl gap align-items=center>
              <div style="width:3.2em;" fl justify-content=center align-items=center>
                <mi mid>logout</mi>
              </div>

              <div>
                <p text std bold>Leave <?= $Squad->name ?></p>
                <p text std>Get out of here</p>
              </div>
            </div>

            <mi midler>east</mi>
          </div>
        </div>
      </a>
    </div>
  </box-model>

</div>