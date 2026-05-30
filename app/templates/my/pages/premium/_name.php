<?php

use Bruder\Heiakim\Model\User\SettingsPremium;
use Bruder\Heiakim\Model\User\UserSettingsPremium;

function format_premium_name_style(string $name)
{
  return ucwords(str_replace("-", " ", $name));
}

$formated_premium_name_style =
  format_premium_name_style($CurrentUser->premium->premium_name_style ?? "");

?>

<div content-width=smol>
  <div mt=wide mb fl gap=mid align-items="center" mb=std>
    <?php include TEMPLATE . "/my/_back_button.php"; ?>

    <label size="mid" has-secondary>
      <div class="label__main">
        <p bold><?= __("Name appearance") ?></p>
      </div>
    </label>
  </div>

  <div>
    <div style="flex:1;">
      <form settings data-form="users:settings,premium,edit">
        <box-model>
          <bm-inr size="wide">

            <div>
              <div mb=smol>
                <p text midler bold><?= __("Color & Style") ?></p>
              </div>

              <div>
                <div>
                  <p text std>
                    <?= __("Choosing one of these styles will change the appearance of your name around the whole website.") ?>
                  </p>
                </div>
              </div>

              <div fl justify-content=center mt=mid>
                <mselect outlined=lighter size=std align=center clickable mselect-type=input-visible>
                  <div class="mselect__inr" fl gap=smol align-items=center>
                    <p mselect-visible-value text bold>
                      <?php if ($CurrentUser->premium->premium_name_style) { ?>
                        <span text std bold class="premium-txt-<?= $CurrentUser->premium->premium_name_style; ?>">
                          <?= $formated_premium_name_style; ?>
                        </span>
                      <?php } else { ?>
                        None
                      <?php } ?>
                    </p>
                    <p text std>
                      <i class="mi">expand_circle_down</i>
                    </p>
                  </div>

                  <mselect-dropdown>
                    <div get-size>
                      <div class="msd__inr">
                        <?php

                        foreach (UserSettingsPremium::$premium_name_styles as $name_style) {

                        ?>

                          <mselect-option submit-closest mselect-input-value="<?= $name_style; ?>"
                            mselect-change-visible-value>
                            <p>
                              <span text bold class=premium-txt-<?= $name_style; ?>>
                                <?= format_premium_name_style($name_style); ?>
                              </span>
                            </p>
                          </mselect-option>

                        <?php

                        }

                        ?>

                      </div>
                    </div>
                  </mselect-dropdown>

                  <input mselect-input type=hidden name=premium_name_style
                    value=<?= $CurrentUser->premium->premium_name_style; ?>>
                </mselect>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </form>
    </div>
  </div>