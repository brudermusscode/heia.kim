<div class="main__menu">
  <div class="page" donotshow cookie-notice animation=zoom-in>
    <div class="icon">
      <mi>cookie</mi>
    </div>
    <div class="hover_card" alignment="center" active=false>
      <div class="hover_card__inr" text-only fl fldircol gap=smol>
        <p text midler bold><?= __("Cookies") ?>?</p>
        <p text std>
          <?= __("Tasty! We feed them to your browser to offer you the best possible experience.") ?>
        </p>
        <div mt fl alic jucsb gap=smol>
          <a href="/legal/privacy#cookies">
            <p text std color=yellow><?= __("Learn more") ?></p>
          </a>
          <mbutton data-action="cookies" data-decision="accept" background=green color=white>
            <p text bold><?= __("Alright") ?>!</p>
          </mbutton>
        </div>
      </div>
    </div>
  </div>
</div>