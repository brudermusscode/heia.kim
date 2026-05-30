<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

use Bruder\Http\Request;

/**
 * @var Request $Request
 */

/**
 * User verified?
 */
if (!VERIFIED)
  exit($Request->error("!UNVERIFIED"));

/**
 * Begin the output buffer.
 */
ob_start();


?>

<style>
  .stars {
    position: fixed;
    z-index: -1;
    height: 100vh;
    width: 100vw;
  }
</style>

<?php

if (ANIMATIONS_ENABLED) {
  echo '<div class="stars">';
  for ($i = 0; $i < 80; $i++)
    echo '<div class="snow"></div>';
  echo '</div>';
}

?>

<form data-form="squad:create" redirect="/manage/squad" responder posrel>
  <div content-width=smoler prompt-height>

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

            <div rounded=mid pblock24 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl align-items=center gap=std>
                  <p text mid>
                    <i class="osu-icon osu-vanilla"></i>
                  </p>
                  <div fl fldircol gap=smol>
                    <p text std bold><?= __("Standard") ?></p>
                    <div fl style=gap:.2em;>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Vanilla</p>
                        </div>
                      </div>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Relax</p>
                        </div>
                      </div>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Autopilot</p>
                        </div>
                      </div>
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

            <div rounded=mid pblock24 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl align-items=center gap=std>
                  <p text mid>
                    <i class="osu-icon osu-taiko"></i>
                  </p>
                  <div fl fldircol gap=smol>
                    <p text std bold>Taiko</p>
                    <div fl style=gap:.2em;>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Vanilla</p>
                        </div>
                      </div>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Relax</p>
                        </div>
                      </div>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Autopilot</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher">
                    </div>
                    <input type="hidden" name="taiko" value="1" />
                    <div fl fldirrow justify-content="center">
                      <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                      </div>
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>

            <div rounded=mid pblock24 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl align-items=center gap=std>
                  <p text mid>
                    <i class="osu-icon osu-ctb"></i>
                  </p>
                  <div fl fldircol gap=smol>
                    <p text std bold>Catch the Beat</p>
                    <div fl style=gap:.2em;>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Vanilla</p>
                        </div>
                      </div>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Relax</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher">
                    </div>
                    <input type="hidden" name="ctb" value="1" />
                    <div fl fldirrow justify-content="center">
                      <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                      </div>
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>

            <div rounded=mid pblock24 pinline12 outlined=darker>
              <div fl gap=wide justify-content="space-between" align-items=center>
                <div fl align-items=center gap=std>
                  <p text mid>
                    <i class="osu-icon osu-mania"></i>
                  </p>
                  <div fl fldircol gap=smol>
                    <p text std bold>Mania</p>
                    <div fl style=gap:.2em;>
                      <div size=smol rounded=wide background=invert>
                        <div pblock12 pinline4>
                          <p text std color=invert>Vanilla</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <toggle-switch toggled="true">
                  <div class="toggle_switch__inr">
                    <div class="toggle_switch__switcher">
                    </div>
                    <input type="hidden" name="mania" value="1" />
                    <div fl fldirrow justify-content="center">
                      <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                      </div>
                    </div>
                  </div>
                </toggle-switch>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div prompt-actions>
        <mbutton size=std material close-overlay>
          <p text>Cancel</p>
        </mbutton>
        <mbutton size=mid background=slight size=mid material submit-closest>
          <p text std bold><?= __("Create it!") ?></p>
        </mbutton>
      </div>
    </box-model>

  </div>
</form>

<?php

die($Request->success(data: ob_get_clean()));
