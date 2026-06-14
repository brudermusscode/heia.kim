<box-model filled elevated>
  <bm-inr size=wide>
    <p text mid bold>Let's take a little tour</p>
    <p text std mt=mid>It's about your privacy, tracking and more. At the end of
      this little trip through space, you will be asked for your consent and you, of course, can decide whether or
      not to accept.</p>
  </bm-inr>
</box-model>

<div fl alic gap title-inline>
  <div flexone>
    <p color=white style="opacity:.6;">Next up: <strong>Privacy</strong></p>
  </div>
  <a href="<?= "$base_url/consent/privacy"; ?>" sub>
    <mbutton mid background=slight-green icon-only data-action="legal:consent,forward"
      data-legal-page="privacy">
      <mi>east</mi>
    </mbutton>
  </a>
</div>