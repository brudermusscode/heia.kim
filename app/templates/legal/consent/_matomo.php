<box-model filled=darker elevated>
  <bm-inr size=wide>
    <div fl alic gap>
      <div style=height:3.2em;width:3.2em; circled fl alic jucc filled=lighter>
        <mi wide>analytics</mi>
      </div>
      <p text mid bold>About Matomo</p>
    </div>
    <p mt=std>
      But what is it? <a class="hyper-bg" extern target=_blank href="https://matomo.org">Matomo <i
          class="ri-link-unlink"></i></a> is an open source web
      analytics platform which offers a privacy respecting alternative to Google Analytics. It tracks people
      around our website and tells us, where to improve. We learn from it. Probably the best about it, it's
      anonymous! IP
      adresses are masked and data, which could be referred to any person is anonymized in such a
      way, it could never be assigned to them.
    </p>
  </bm-inr>

  <box-model filled=lighter>
    <bm-inr size=mid>
      <div fl gap=smol+ alistart>
        <mi>privacy_tip</mi>
        <p>
          You can opt out of the tracking at any time through our <a class="hyper-bg"
            href="/legal/privacy#matomo">Privacy
            Policy <i class="ri-link-unlink"></i></a> page or by setting the "Do Not Track" setting inside of your
          browser settings, which the most modern browsers should have.
        </p>
      </div>
    </bm-inr>
  </box-model>
</box-model>

<div fl alic gap title-inline>
  <div flexone>
    <p color=white style="opacity:.6;">Next up: <strong>Accept or decline</strong></p>
  </div>
  <a href="<?= "$base_url/consent/acceptance"; ?>" sub>
    <mbutton mid background=slight-green icon-only data-action="legal:consent,forward"
      data-legal-page="acceptance">
      <mi>east</mi>
    </mbutton>
  </a>
</div>