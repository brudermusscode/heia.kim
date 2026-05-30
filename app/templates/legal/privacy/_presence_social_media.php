<div fl fldircol gap=smol+>
  <div title-inline>
    <p text mid bold color=white><?= __("Social media presence") ?></p>
  </div>

  <box-model rounded=wide elevated>
    <bm-inr size=wide>
      <p text mt=std>
        <?= __("legal.social.1") ?>
      </p>

      <p text mt=std>
        <?= __("legal.social.2") ?>
      </p>

      <p text mt=std>
        <?= __("legal.social.3") ?>
      </p>

      <p text mt=std>
        <?= __("legal.social.4") ?>
      </p>

      <p text mt=std>
        <?= __("legal.social.5") ?>
      </p>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Types of data processed") ?></strong>
        <p>
          <?= __("Contact information (e.g. email, phone numbers)") ?>;
          <?= __("Content data (e.g. entries in online forms)") ?>;
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
          <?= __("Contact Requests and Communication") ?>;
          <?= __("Feedback (e.g. collecting feedback via online form)") ?>; <?= __("Marketing") ?>.
        </p>
      </div>

      <div class="legal_indent" text mt=std>
        <strong><?= __("Legal bases") ?></strong>
        <div>
          <?= __("Legitimate Interests") ?>
          <div class="legal_para"><?= __("Art. 6 (1) sentence 1 lit. f) GDPR") ?></div>
        </div>
      </div>

      <div text mt=wide>
        <div class="legal_para" inline><?= __("Further information on processing, procedures and services") ?>
        </div>

        <div class="legal_indent" text mt=std>
          <strong>Twitter</strong>
          <div>
            <p>
              Social network; Service Provider: Twitter International Company, One Cumberland Place, Fenian Street,
              Dublin 2 D02 AX07, Ireland, Parent Company: Twitter Inc., 1355 Market Street, Suite 900, San
              Francisco, CA 94103, USA;
            </p>
            <div mt=smol>
              <strong><?= __("Legal bases") ?></strong>
              <div>
                <?= __("Legitimate Interests") ?>
                <div class="legal_para"><?= __("Art. 6 (1) sentence 1 lit. f) GDPR") ?></div>
              </div>
            </div>
          </div>

          <div fl justify-content=start mt=std gap=smol>
            <a extern target=_blank href="https://twitter.com/privacy">
              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-link-unlink"></i></p>
                <p>Twitter <?= __("Privacy Policy") ?></p>
              </div>
            </a>

            <a extern target=_blank href="https://twitter.com/personalization">
              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-link-unlink"></i></p>
                <p>Twitter <?= __("Settings") ?></p>
              </div>
            </a>
          </div>
        </div>

        <div class="legal_indent" text mt=std>
          <strong>YouTube</strong>
          <div>
            <p>
              Social Network and Video Platform; Service Provider: Google Ireland Limited, Gordon House, Barrow
              Street, Dublin 4, Ireland
            </p>
            <div mt=smol>
              <strong><?= __("Legal bases") ?></strong>
              <div>
                <?= __("Legitimate Interests") ?>
                <div class="legal_para"><?= __("Art. 6 (1) sentence 1 lit. f) GDPR") ?></div>
              </div>
            </div>
          </div>

          <div fl justify-content=start mt=std gap=smol>
            <a extern target=_blank href="https://adssettings.google.com/authenticated">
              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-link-unlink"></i></p>
                <p>Google Opt-Out</p>
              </div>
            </a>

            <a extern target=_blank href="https://policies.google.com/privacy">
              <div class="legal_tag" has-tooltip>
                <p icon><i class="ri-link-unlink"></i></p>
                <p>Google <?= __("Privacy Policy") ?></p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </bm-inr>
  </box-model>
</div>