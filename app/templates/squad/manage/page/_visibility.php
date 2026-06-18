<?php

$publicity_text = CurrentUser->squad->joinable === 0
  ? "Private"
  : (CurrentUser->squad->joinable === 1
    ?  "Request only"
    : "Public for all");

?>

<content std minlineauto>
  <div mt=wide mb fl gap=mid align-items="center" mb=std>
    <?php include TEMPLATE . "/squad/manage/_back-button.php"; ?>
    <p text mid bold>Publicity</p>
  </div>

  <box-model>
    <form request="squad:update" delay=20 responder=error>
      <bm-inr size=wide>
        <div>
          <div fl gap=mid align-items=center>
            <div style=line-height:1.2em;flex:1;>
              <p text std>Set the publicity of your squad
                for new members. This won't have effect
                to the current members.</p>
            </div>

            <mselect outlined=lighter size=mid align=center clickable mselect-type=input-visible>
              <div class=mselect__inr fl alic gap=smol>
                <p mselect-visible-value text bold><?= $publicity_text; ?></p>
                <i class="mi" size=smol>expand_all</i>
              </div>
              <mselect-dropdown>
                <div get-size>
                  <div class=msd__inr>
                    <mselect-option submit-closest mselect-input-value="2" mselect-change-visible-value>
                      <p>Public for all</p>
                    </mselect-option>
                    <mselect-option submit-closest mselect-input-value="1" mselect-change-visible-value>
                      <p>Request only</p>
                    </mselect-option>
                    <mselect-option submit-closest mselect-input-value="0" mselect-change-visible-value>
                      <p>Private</p>
                    </mselect-option>
                  </div>
                </div>
              </mselect-dropdown>
              <input mselect-input type=hidden name=joinable value="<?= CurrentUser->squad->joinable; ?>" />
            </mselect>
          </div>
        </div>
      </bm-inr>
    </form>
  </box-model>
</content>