<div fl fldircol gap=smol+>
  <div title-inline>
    <p text mid bold color=white><?= __("Web Analysis, Monitoring & Optimization") ?></p>
  </div>

  <box-model rounded=wide elevated>
    <bm-inr size=wide>
      <p text mt=std>
        <?= __("legal.anal.1") ?>
      </p>

      <p text mt=std>
        <?= __("legal.anal.2") ?>
      </p>

      <p text mt=std>
        <?= __("legal.anal.3") ?>
      </p>

      <p text mt=std>
        <?= __("legal.anal.4") ?>
      </p>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Types of data processed") ?></strong>
        <p>
          <?= __("Usage data (e.g. websites visited, interest in content, access times)") ?>;
          <?= __("Meta/communication data (e.g. device information, IP addresses)") ?>.
        </p>
      </div>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Affected people") ?></strong>
        <p>
          <?= __("Users (e.g. website visitors, users of online services)") ?>.
        </p>
      </div>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Purposes of processing") ?></strong>
        <p>
          <?= __("Range measurement (e.g. access statistics, detection of returning visitors)") ?>;
          <?= __("Profiles with user-related information (creating user profiles)") ?>.
        </p>
      </div>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Safety measures") ?></strong>
        <p>
          <?= __("IP-Masking (Pseudonymization of the IP address)") ?>.
        </p>
      </div>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Legal bases") ?></strong>
        <div>
          <?= __("Legitimate Interests") ?> <div class="legal_para">
            <?= __("Art. 6 (1) sentence 1 lit. f) GDPR") ?></div>
        </div>
      </div>

      <div text mt=wide>
        <div class="legal_para" inline><?= __("Further information on processing, procedures and services") ?>
        </div>

        <div class="legal_indent" text mt=std>
          <strong><?= __("Matomo (without cookies)") ?></strong>
          <div>
            <p>
              <?= __("legal.matomo.1") ?>
            </p>
            <div mt=smol>
              <strong><?= __("Legal bases") ?></strong>
              <div>
                <?= __("Legitimate Interests") ?>
                <div class="legal_para"><?= __("Art. 6 (1) sentence 1 lit. f) GDPR") ?></div>
              </div>
            </div>

            <?php if (PROD) { ?>
              <div mt=std>
                <div id="matomo-opt-out"></div>
                <script src="https://analytics.heia.kim/index.php?module=CoreAdminHome&action=optOutJS&divId=matomo-opt-out&language=auto&backgroundColor=FFFFFF&fontColor=000000&fontSize=16px&fontFamily=Segoe ui&showIntro=1">
                </script>
              </div>
            <?php } ?>

            <div fl justify-content=start mt=std>
              <a extern target=_blank href="https://matomo.org/">
                <div class="legal_tag" has-tooltip>
                  <p icon><i class="ri-link-unlink"></i></p>
                  <p><?= __("Matomo") ?></p>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </bm-inr>
  </box-model>
</div>