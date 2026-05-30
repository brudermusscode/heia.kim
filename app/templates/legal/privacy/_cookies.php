      <div fl fldircol gap=smol+>
        <div title-inline>
          <p text mid bold color=white><?= __("Use of Cookies") ?></p>
        </div>

        <box-model rounded=wide elevated>
          <bm-inr size=wide>

            <p text mt=std>
              <?= __("legal.cookies.1") ?>
            </p>

            <div text mt=std>
              <div class="legal_para" inline><?= __("Notes on Consent") ?></div>
              <p>
                <?= __("Notes on Consent Text") ?>
              </p>
            </div>

            <div text mt=std>
              <div class="legal_para" inline><?= __("Notes on legal bases for data protection") ?></div>
              <p>
                <?= __("Notes on legal bases for data protection Text") ?>
              </p>
            </div>

            <div text mt=std>
              <div class="legal_para" inline><?= __("Duration of storage") ?></div>
              <p>
                <?= __("Duration of storage Text") ?>
              </p>
            </div>

            <div class="legal_indent" text mt=std>
              <strong><?= __("Temporary cookies (session cookies)") ?></strong>
              <p>
                <?= __("Temporary cookies Text") ?>
              </p>
            </div>

            <div class="legal_indent" text mt=std>
              <strong><?= __("Permanent cookies") ?></strong>
              <p>
                <?= __("Permanent cookies Text") ?>

              </p>
            </div>

            <div text mt=std>
              <div class="legal_para" inline>
                <?= __("General information on revocation and objection (opt-out)") ?></div>
              <p>
                <?= __("legal.cookies.general.1") ?>
              </p>
            </div>

            <div text mt=wide>
              <div class="legal_para" inline>
                <?= __("Further information on processing, procedures and services") ?></div>

              <div class="legal_indent" text mt=std>
                <strong><?= __("Processing of cookie data based on consent") ?></strong>
                <p>
                  <?= __("legal.cookies.processing.1") ?>
                </p>
              </div>

              <div class="legal__mentions" mt=mid>
                <div class="legal__mentions_inr" fl justify-content=space-between align-items=center gap=wide>
                  <div flexone>
                    <p>
                      <?= __("We surely give you the option to opt-in or opt-out from our usage of cookies when you should have changed your mind. Just use this lovely little toggle switch.") ?>
                    </p>
                  </div>

                  <toggle-switch toggled="<?= COOKIE_CONSENT ? 'true' : 'false'; ?>" action="privacy:cookie-consent">
                    <div class="toggle_switch__inr">
                      <div class="toggle_switch__switcher" toggled="<?= COOKIE_CONSENT ? 'true' : 'false'; ?>">
                      </div>
                      <input type="hidden" value="<?= COOKIE_CONSENT ? 1 : 0; ?>" />
                      <div fl fldirrow justify-content="center">
                        <div fl fldirrow justify-content="space-between" align-items="center" style="width:calc(100% - .8em);">
                        </div>
                      </div>
                    </div>
                  </toggle-switch>
                </div>
              </div>
            </div>
          </bm-inr>
        </box-model>
      </div>