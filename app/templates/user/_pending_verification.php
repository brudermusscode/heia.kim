<div class="floating_container">
  <div tac style="max-width:600px;" fl fldircol gap=mid alic>
    <div fl fldircol gap alic>
      <div style=height:5.2em;width:5.2em; filled circled fl alic jucc>
        <mi size=wide>egg</mi>
      </div>
      <div fl fldircol gap=smolest>
        <p text bold wide><?= __("Pending verification") ?></p>
        <p text std>
          <?= __("This player has not yet verified their account. Come back later!") ?></p>
      </div>
    </div>
    <mbutton onclick="history.go(-1);" material size=mid has-icon=left outlined>
      <i class="mi">arrow_back</i>
      <p text std bold><?= __("Go back") ?></p>
    </mbutton>
  </div>
</div>