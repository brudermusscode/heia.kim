<box-model filled elevated class=changed-mind>
  <bm-inr size=wide fl fldircol gap>
    <p justcontcent text bold wide style="font-size:4.2em;">
      <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_wcjvpjt7.json" background="transparent"
        speed=".9" style="width: 120px; height: 120px;" loop autoplay></lottie-player>
    </p>

    <div fl fldircol alic gap>
      <p text mid bold>That's all!</p>
      <p tac>We hope to have you caught up with all the information needed. Now it's on you to get the full
        experience.</p>
    </div>

    <div fl jucc alic gap>
      <a href="<?= $base_url; ?>">
        <mbutton size=std background=clean material>
          <p text smol>I don't accept</p>
        </mbutton>
      </a>

      <form data-form='users:settings,edit' responder>
        <input type=hidden name=accepts_policies value=1 />
        <mbutton size=mid submit-closest background=slight-green material>
          <p text std bold color=dark-green>I accept!</p>
        </mbutton>
      </form>
    </div>
  </bm-inr>
</box-model>