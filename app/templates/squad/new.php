<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

authorize(CurrentUser);

if (CurrentUser->squad) die(error("!HAS_SQUAD"));

ob_start();

include SNOW; ?>

<form request="squad:create" redirect="/manage/squad"
  update-user-references responder posrel>
  <content stdplus>
    <box-model prompt elevated rounded=wide filled=lighter>
      <div prompt-content>
        <div prompt-header>
          <mi>workspaces</mi>
          <p title>Create Squad</p>
        </div>

        <div prompt-inner-content fl fldircol gap>
          <div fl gap>
            <div style=flex-basis:30%;>
              <div input material has-icon>
                <mi>label</mi>
                <input autofocus tabindex=1 type="text" name="tag" placeholder="Tag" enter-submitable maxlength="6" />
              </div>
            </div>

            <div style=flex:1;>
              <div input material has-icon>
                <mi>text_fields</mi>
                <input tabindex=2 type="text" name="name" placeholder="Name" enter-submitable maxlength="16" />
              </div>
            </div>
          </div>

          <div fl fldircol gap=smol>
            <p text bold std><?= __("Open for all") ?></p>
            <div fl gap=mid justify-content="space-between" align-items=center>
              <div fl align-items=center gap=std>
                <div style=line-height:1.2em;>
                  <p>
                    <?= __("If checked, everyone can join. Unchecked, you will receive join requests from people who want to join, which you can accept or deny.") ?>
                  </p>
                </div>
              </div>

              <toggle-switch toggled="false">
                <div class="toggle_switch__inr">
                  <div class="toggle_switch__switcher">
                  </div>
                  <input type="hidden" name="joinable" value="0" />
                  <div fl fldirrow justify-content="center">
                    <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </div>
              </toggle-switch>
            </div>
          </div>

          <div fl fldircol gap=smoler>
            <div lh1 mb=smol>
              <p text bold std><?= __("Modes") ?></p>
              <p text std><?= __("These will be relevant for scoring and climbing the leaderboard.") ?></p>
            </div>

            <div rounded pblock18 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl alic gap=smol>
                  <mi mid class="osu-icon osu-vanilla" style="width:64px;"></mi>
                  <div fl fldircol gap=smol>
                    <p text std bold><?= __("Standard") ?></p>
                    <div fl alic gap=smoler>
                      <p text std color=invert color=invert rounded=wide background=invert pinline8 pblock2>Vanilla</p>
                      <p text std color=invert rounded=wide background=invert pinline8 pblock2>Relax</p>
                      <p text std color=invert color=invert rounded=wide background=invert pinline8 pblock2>Autopilot</p>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher">
                    </div>
                    <input type="hidden" name="osu" value="1" />
                    <div fl fldirrow justify-content="center">
                      <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                      </div>
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>

            <div rounded pblock18 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl alic gap=smol>
                  <mi mid style="width:64px;" class="osu-icon osu-taiko"></mi>
                  <div fl fldircol gap=smol>
                    <p text std bold>Taiko</p>
                    <div fl alic gap=smoler>
                      <p text std color=invert color=invert rounded=wide background=invert pinline8 pblock2>Vanilla</p>
                      <p text std color=invert rounded=wide background=invert pinline8 pblock2>Relax</p>
                      <p text std color=invert color=invert rounded=wide background=invert pinline8 pblock2>Autopilot</p>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher"></div>
                    <input type="hidden" name="taiko" value="1" />
                    <div fl fldirrow jucsb alic style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>

            <div rounded=mid pblock18 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl alic gap=smol>
                  <mi mid style="width:64px;" class="osu-icon osu-ctb"></mi>
                  <div fl fldircol gap=smol>
                    <p text std bold>Catch The Beat</p>
                    <div fl alic gap=smoler>
                      <p text std color=invert color=invert rounded=wide background=invert pinline8 pblock2>Vanilla</p>
                      <p text std color=invert rounded=wide background=invert pinline8 pblock2>Relax</p>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher"></div>
                    <input type="hidden" name="ctb" value="1" />
                    <div fl fldirrow jucsb alic style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>

            <div rounded=mid pblock18 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl alic gap=smol>
                  <mi mid style="width:64px;" class="osu-icon osu-mania"></mi>
                  <div fl fldircol gap=smol>
                    <p text std bold>Mania</p>
                    <div fl alic gap=smoler>
                      <p text std color=invert color=invert rounded=wide background=invert pinline8 pblock2>Vanilla</p>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher"></div>
                    <input type="hidden" name="mania" value="1" />
                    <div fl fldirrow jucsb alic style="width:calc(100% - .8em);">
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div prompt-actions>
        <mbutton close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <mbutton mid background=company color=company-text has-icon=right submit-closest>
          <?= __("Create it") ?>
          <mi>arrow_forward</mi>
        </mbutton>
      </div>
    </box-model>
  </content>
</form>

<?php die(success(data: ob_get_clean()));
