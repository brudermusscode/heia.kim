<box-model filled=darker elevated>
  <bm-inr size=wide>
    <div fl alic gap>
      <div style=height:3.2em;width:3.2em; circled fl alic jucc filled=lighter>
        <mi wide>gpp_maybe</mi>
      </div>
      <p text mid bold>About Privacy</p>
    </div>
    <p mt=std>
      We need to tell you about our new <a class="hyper-bg" href="/legal/privacy">Privacy
        Policies <i class="ri-link-unlink"></i></a>,
      which
      tells you about the data we collect
      and how we use them. You might want to take a look at it before you accept it. <strong>We need your consent for
        you to be able to use <?= APP_NAME; ?> properly.</strong>
    </p>
  </bm-inr>
</box-model>

<div fl alic gap title-inline>
  <div flexone>
    <p color=white style="opacity:.6;">Next up: <strong>Cookies</strong></p>
  </div>
  <a href="<?= "$base_url/consent/cookies"; ?>" sub>
    <mbutton mid background=slight-green icon-only data-action="legal:consent,forward"
      data-legal-page="cookies">
      <mi>east</mi>
    </mbutton>
  </a>
</div>