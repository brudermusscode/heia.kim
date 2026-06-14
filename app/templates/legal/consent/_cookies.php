<box-model filled=darker elevated>
  <bm-inr size=wide>
    <div fl alic gap>
      <div style=height:3.2em;width:3.2em; circled fl alic jucc filled=lighter>
        <mi wide>cookie</mi>
      </div>
      <p text mid bold>About Cookies</p>
    </div>
    <p mt=std>
      We love to feed them to your browser, because <strong></strong>it makes the experience you have alot
      better</strong>! We are trying
      hard to improve the visual experience and in alot of cases, we need to set cookies to save information
      about things that are going on on <?= APP_NAME; ?>. You might have given us your consent through
      the little prompt in the menu bar. But if you didn't or you want to revoke it, go ahead and tick the
      little cute switcher down under.
    </p>
  </bm-inr>
  <box-model filled=lighter>
    <bm-inr size=mid>
      <div fl gap=smol+ alistart>
        <mi>privacy_tip</mi>
        <p>We will still set some cookies that are really necessary. But nothing you need to worry
          about, <strong>promised</strong>!</p>

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
    </bm-inr>
  </box-model>
</box-model>

<div fl alic gap title-inline>
  <div flexone>
    <p color=white style="opacity:.6;">Next up: <strong>Matomo</strong></p>
  </div>
  <a href="<?= "$base_url/consent/matomo"; ?>" sub>
    <mbutton mid background=slight-green icon-only data-action="legal:consent,forward"
      data-legal-page="matomo">
      <mi>east</mi>
    </mbutton>
  </a>
</div>